<?php
namespace App\Services;

use Composer\Composer;
use Composer\Config;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\IO\NullIO;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Repository\RepositoryFactory;
use Composer\Repository\RepositoryManager;
use Composer\Repository\Vcs\VcsDriverInterface;
use Composer\Repository\VcsRepository;
use Composer\Satis\PackageSelection\PackageSelection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

class RepositoryService
{
    private $tempDir = '/tmp';

    public function getSatisConfig(): array
    {
        return [
            "name" => config('app.repo_name'),
            "description" => config('app.repo_desc'),
            "homepage" => config('app.php_base_url'),
            "repositories" => [
                [
                    "type" => "composer",
                    "url" => config('app.php_base_url')
                ],
            ],
        ];
    }

    public function getComposerConfig(): array
    {
        // Generate a random hash we can ignore :)
        $hash = sha1(time());

        return [
            "packages" => [],
            "includes" => [
                "include/all$".$hash.".json" => [
                    "sha1" => $hash
                ]
            ]
        ];
    }

    public function getRuntimeConfig(): Config
    {
        $user = Auth::user();

        // TODO: here is a problem, the REST Api user is not a repository user

        // Remove all the default repositories
        Config::$defaultRepositories = [];

        // Create a composer config and add any tokens required
        $runtimeConfig = new Config();
        $runtimeConfig->merge([
            'config' => [
                'home' => $this->tempDir,
                'gitlab-token' => 'TODO_PUT_TOKEN_HERE',
            ]
        ]);

        return $runtimeConfig;
    }

    public function isRepositoryValid(string $url, string $type): bool
    {
        try{
            $io = new NullIO();
            $config = Factory::createConfig();
            $io->loadConfiguration($config);
            $repository = new VcsRepository(['url' => $url, 'type' => $type], $io, $config);

            /** @var VcsDriverInterface $driver */
            $driver = $repository->getDriver();
            if(!$driver) return false;

            $information = $driver->getComposerInformation($driver->getRootIdentifier());

            return !empty($information['name']);
        }catch(\Exception $exception){
            error_log("Exception: " . $exception->getMessage());
            return false;
        }
    }

    public function scan(string $url, string $type): array
    {
        // Create a set of console outputs that we can use with the composer objects
        $input = new ArrayInput([]);
        $output = new ConsoleOutput();
        $output->setVerbosity(ConsoleOutput::VERBOSITY_VERY_VERBOSE);
        $helperSet = new HelperSet();
        $io = new ConsoleIO($input, $output, $helperSet);

        $composerConfig = $this->getRuntimeConfig();

        // initialize composer
        $composer = new Composer();
        $composer->setConfig($composerConfig);

        // Create a repository manager with the data passed into the function
        /** @var RepositoryManager $rm */
        $repositoryManager = RepositoryFactory::manager($io, $composerConfig);
        $repo = $repositoryManager->createRepository($type, ['type' => $type, 'url' => $url]);
        $repositoryManager->addRepository($repo);

        // Associate the repository manager with the composer object
        $composer->setRepositoryManager($repositoryManager);

        // Obtain a satis config and then select all the packages from this new repository
        $satisConfig = $this->getSatisConfig();
        $packageSelection = new PackageSelection($output, $this->tempDir, $satisConfig, false);
        $packages = $packageSelection->select($composer, true);

        // Extract and dump all the data so it can be returned
        $packagesByName = [];
        $dumper = new ArrayDumper();
        foreach ($packages as $item) {
            $packagesByName[$item->getName()][$item->getPrettyVersion()] = $dumper->dump($item);
        }

        return $packagesByName;
    }
}
