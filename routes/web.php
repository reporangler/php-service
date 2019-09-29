<?php
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

// Healthcheck for any monitoring software
$router->get('/healthz', 'DefaultController@healthz');

$router->group(['middleware' => ['cors']], function() use ($router) {
    // Set the CORS options that we will allow web requests from (This doesn't affect composer/console clients)
    $router->options('{path:.*}', 'DefaultController@cors');

    // Pass all requests through the auth layer
    $router->group(['middleware' => 'auth:repo'], function() use ($router) {
        // Repository data
        $router->get('/packages.json', 'ComposerController@repository');

        // Return all packages the authenticated user (public or an actual user) is allowed to access
        $router->get('/include/{hash}', 'ComposerController@packages');
    });

    $router->group(['middleware' => 'auth:token'], function() use ($router) {
        // Routes to manage packages
        $router->post('/', 'PackageController@publish');
        $router->put('/', 'PackageController@update');
        $router->delete('/', 'PackageController@remove');
    });
});
