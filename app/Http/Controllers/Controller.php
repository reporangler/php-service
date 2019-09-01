<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    public function cors($args)
    {
        return $this->healthz();
    }

    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.php_base_url')], 200);
    }

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

    public function packages(Request $request)
    {
        $u = Auth::user();

        return file_get_contents("/www/all$12341234.json");
    }
}
