<?php

namespace App\Http\Controllers;

use App\Services\RepositoryService;
use Composer\Repository\InvalidRepositoryException;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use RepoRangler\Services\MetadataClient;

class PackageController extends BaseController
{
    public function publish(Request $request, RepositoryService $repoService, MetadataClient $metadataClient)
    {
        /** @var  $user */
        $user = Auth::guard('token')->user();

        $types = [
            'composer', 'vcs', 'package', 'pear',
            'git', 'git-bitbucket', 'github', 'gitlab',
            'svn', 'fossil', 'perforce',
            'hg', 'hg-bitbucket',
            'artifact', 'path',
        ];

        $schema = [
            'url' => 'required|url',
            'type' => 'required|in:'.implode(',', $types),
            'package_group' => 'required|string',
        ];

        $data = $this->validate($request,$schema);

        if(!$repoService->isRepositoryValid($data['url'], 'vcs')){
            throw new InvalidRepositoryException();
        }

        // Scanning might take a while, reset the clock to 60 seconds
        set_time_limit(60);
        $packages = $repoService->scan($data['url'], $data['type']);

        $repositoryType = config('app.repo_type');

        foreach($packages as $item){
            foreach($item as $version){
                // Each time a package is updated, reset the timeout for another 60 seconds
                set_time_limit(60);

                // This then will keep a 60 second window for each package to publish,
                // but not allow the system an unlimited timeout
                $metadataClient->addPackage(
                    $user->token,
                    $repositoryType,
                    $data['package_group'],
                    $version['name'],
                    $version['version'],
                    $version
                );
            }
        }

        // TODO: check the package group exists
        // TODO: only REST enabled authenticated users are allowed to publish
        // TODO: check the user permission object if this user is allowed to publish into the requested group

        return new JsonResponse($packages);
    }

    public function update()
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function remove()
    {
        return new JsonResponse(['method' => __METHOD__]);
    }
}
