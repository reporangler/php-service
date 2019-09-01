<?php

namespace App\Providers;

use App\Services\AuthClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class);

        $this->app->bind(AuthClient::class, function(Application $app){
            $baseUrl = config('app.auth_base_url');

            $httpClient = $app->make(Client::class);

            return new AuthClient($baseUrl, $httpClient);
        });
    }
}
