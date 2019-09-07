<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use RepoRangler\Entity\AuthenticatedUser;
use RepoRangler\Entity\PublicUser;
use RepoRangler\Services\AuthClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('api', function ($request) {
            $authClient = app(AuthClient::class);

            $auth_type = $request->headers->get('php-auth-type', 'http-basic');
            $auth_user = $request->headers->get('php-auth-user');
            $auth_password = $request->headers->get('php-auth-pw');

            if(in_array(null, [$auth_user, $auth_password])){
                return app(PublicUser::class);
            }

            $response = $authClient->login($auth_type, $auth_user, $auth_password);
            $json = json_decode((string)$response->getBody(), true);

            return app(AuthenticatedUser::class, $json);
        });
    }
}
