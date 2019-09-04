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
                return new PublicUser();
            }

            $response = $authClient->login($auth_type, $auth_user, $auth_password);
            $json = json_decode((string)$response->getBody(), true);

            return new AuthenticatedUser($json);
        });
    }
}
