<?php

$required = [];
foreach(['APP_NAME', 'APP_PROTOCOL', 'APP_DOMAIN'] as $key){
    $value = env($key);
    if($value === null) throw new Exception("The parameter '$key' cannot be empty'");
    $required[$key] = $value;
}

$url = "{$required['APP_PROTOCOL']}://{$required['APP_NAME']}.{$required['APP_DOMAIN']}";

return [
    'debug' => env('APP_DEBUG', false),
    'protocol' => $required['APP_PROTOCOL'],
    'url' => env('APP_URL', $url),
    'domain' => env('APP_DOMAIN', $required['APP_DOMAIN']),
];
