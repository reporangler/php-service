<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use RepoRangler\Services\MetadataClient;

class ComposerController extends BaseController
{
    public function repository()
    {
        // Generate a random hash we can ignore :)
        $hash = sha1(time());

        return new JsonResponse([
            "packages" => [],
            "includes" => [
                "include/all$".$hash.".json" => [
                    "sha1" => $hash
                ]
            ]
        ], 200);
    }

    public function packages(MetadataClient $metadata)
    {
        $user = Auth::user();

        $packages = $metadata->getPackages($user->token);

        $document = ["packages" => []];

        foreach($packages as $item){
            $document['packages'][$item['name']][$item['version']] = $item['definition'];
        }

        return new JsonResponse($document, 200);
    }
}
