<?php

$required = [];
foreach(['APP_NAME', 'APP_PROTOCOL', 'APP_DOMAIN'] as $key){
    $value = env($key);
    if($value === null) throw new Exception("The env-var '$key' cannot be empty'");
    $required[$key] = $value;
}

return [
    'debug' => env('APP_DEBUG', false),
    'protocol' => $required['APP_PROTOCOL'],
    'domain' => env('APP_DOMAIN', $required['APP_DOMAIN']),
    'repository_type' => $required['APP_NAME'],

    'php_base_url'  => env('APP_PHP_URL',   "{$required['APP_PROTOCOL']}://{$required['APP_NAME']}.{$required['APP_DOMAIN']}"),
    'auth_base_url' => env('APP_AUTH_URL',  "{$required['APP_PROTOCOL']}://auth.{$required['APP_DOMAIN']}"),
];
