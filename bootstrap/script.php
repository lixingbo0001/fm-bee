<?php

include "../vendor/autoload.php";

$app = new \Core\Application(dirname(realpath(__DIR__)));

$app->bind('nsq', function ($app) {
    return new \Core\Serve\NsqHandle($app);
});

$app->handle(config('queue.driver'));
