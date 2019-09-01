<?php

namespace App\Providers;

use App\Entity\AuthenticatedUser;
use App\Entity\PublicUser;
use App\Services\AuthClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        Auth::viaRequest('api', function ($request) {
            // new AuthenticatedUser();

            $authClient = app(AuthClient::class);

            $response = $authClient->login(
                $request->headers->get('php-auth-type', 'http-basic'),
                $request->headers->get('php-auth-user'),
                $request->headers->get('php-auth-pw')
            );

            error_log("RESPONSE = ".(string)$response->getBody());

            return new PublicUser();
        });
    }
}
