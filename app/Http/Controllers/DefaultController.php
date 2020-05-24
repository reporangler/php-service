<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class DefaultController extends BaseController
{
    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.php_base_url')], 200);
    }

    public function cors()
    {
        $this->healthz();
    }
}
