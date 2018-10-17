<?php

//use GuzzleHttp\Client as GuzzleClient;

/**
 * https://symfony.com/doc/current/security/api_key_authentication.html
 * http://symfony.com/doc/current/security/guard_authentication.html#the-guard-authenticator-methods.
 * https://emirkarsiyakali.com/implementing-jwt-authentication-to-your-api-platform-application-885f014d3358.
 */
require __DIR__.'/../vendor/autoload.php';

$guzzleclient = new \GuzzleHttp\Client();

$res = $guzzleclient->get('http://127.0.0.1:8000/api/v1/users/bob/coupons', [
    'auth' => ['bob', 'xxxxxxx'],
]);

$result = json_decode($res->getBody(), true);

var_dump($result);
