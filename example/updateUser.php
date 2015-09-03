<?php

require __DIR__ . "/../vendor/autoload.php";

use Grovo\Api\Client\GrovoApi;

$clientId = '5FCE1A21s8f80A96o7E6gAIkucy938';
$clientSecret = '8uXnInPfsXKc6087YE83s8z74B7oyY';
$accessToken = ''; //OPTIONAL

$client = new GrovoApi($clientId, $clientSecret, $accessToken, function ($newToken) {
    // This will be invoked if your existing token has expired
    // or you did not provide one originally.
    //
    // Use this callback to persist this token somewhere so you
    // can pass it in in the future.
});

$parameters = [
    'email' => 'partner@grovo.com',
    'first_name' => 'Grovo',
    'last_name' => 'Partner'
];

$response = $client->updateUser(43, $parameters);

print_r($response);