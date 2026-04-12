<?php

namespace App\Http\Controllers;

use App\Services\RepositoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use RepoRangler\Services\MetadataClient;

class ComposerController extends BaseController
{
    public function repository(RepositoryService $repoService)
    {
        return new JsonResponse($repoService->getComposerConfig());
    }

    public function packages(MetadataClient $metadata)
    {
        $user = Auth::guard('repo')->user();

        $repositoryType = config('app.repo_type');

        $packages = $metadata->getPackages($user->token, $repositoryType);

        $document = ["packages" => []];

        foreach($packages as $item){
            $document['packages'][$item['name']][$item['version']] = $item['definition'];
        }

        return new JsonResponse($document);
    }
}
