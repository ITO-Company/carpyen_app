<?php

return [
    'pagofacil' => [
        'token_service' => env('PAGOFACIL_TOKEN_SERVICE', '51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba'),
        'token_secret' => env('PAGOFACIL_TOKEN_SECRET', '0C351C6679844041AA31AF9C'),
        'base_url' => env('PAGOFACIL_BASE_URL', 'https://serviciostigomoney.pagofacil.com.bo/api'),
    ],
];
