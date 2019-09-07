<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class PackageController extends BaseController
{
    public function publish(Request $request)
    {
        $schema = [
            'url' => 'required|string',
            'package_group' => 'required|string',
        ];

        $data = $this->validate($request,$schema);

        return new JsonResponse(['method' => $data], 200);
    }

    public function update()
    {
        return new JsonResponse(['method' => __METHOD__], 200);
    }

    public function remove()
    {
        return new JsonResponse(['method' => __METHOD__], 200);
    }
}
