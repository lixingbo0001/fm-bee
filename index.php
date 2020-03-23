<?php

include "vendor/autoload.php";

$app = new \Core\Application(realpath(__DIR__));

$app->bind('http', function ($app) {
    return new \Core\Serve\HttpHandle($app);
});

$app->handle('http');