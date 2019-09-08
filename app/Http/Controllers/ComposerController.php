<?php

namespace App\Http\Controllers;

use App\Services\RepositoryService;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use RepoRangler\Services\MetadataClient;

class ComposerController extends BaseController
{
    public function repository(RepositoryService $repoService)
    {
        return new JsonResponse($repoService->getComposerConfig(), 200);
    }

    public function packages(MetadataClient $metadata)
    {
        $user = Auth::user();

        $repositoryType = config('app.repository_type');

        $packages = $metadata->getPackages($user->token, $repositoryType);

        $document = ["packages" => []];

        foreach($packages as $item){
            $document['packages'][$item['name']][$item['version']] = $item['definition'];
        }

        return new JsonResponse($document, 200);
    }
}
