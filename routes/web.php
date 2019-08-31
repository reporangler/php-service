<?php
use \Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => ['cors']], function() use ($router) {
    // Set the CORS options that we will allow web requests from (This doesn't affect composer/console clients)
    $router->options('{path:.*}', 'Controller@cors');

    // Healthcheck for any monitoring software
    $router->get('/healthz', 'Controller@healthz');

    // Repository data
    $router->get('/packages.json', 'Controller@repository');

    // Pass all requests through the auth layer
    $router->group(['middleware' => ['auth']], function() use ($router) {
        // Return all packages the authenticated user (public or an actual user) is allowed to access
        $router->get('/include/{hash_to_ignore}', 'Controller@packages');
    });
});
