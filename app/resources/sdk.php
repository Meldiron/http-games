<?php

use Appwrite\Client;
use Appwrite\Services\TablesDB;
use Appwrite\Services\Users;
use Utopia\App;

App::setResource('sdk', function () {
    $sdk = new Client;

    $sdk
        ->setEndpoint($_ENV['_APP_APPWRITE_ENDPOINT'])
        ->setKey($_ENV['_APP_APPWRITE_KEY'])
        ->setProject($_ENV['_APP_APPWRITE_PROJECT_ID']);

    return $sdk;
});

App::setResource('sdkForUsers', function (Client $sdk) {
    return new Users($sdk);
}, ['sdk']);

App::setResource('sdkForTables', function (Client $sdk) {
    return new TablesDB($sdk);
}, ['sdk']);
