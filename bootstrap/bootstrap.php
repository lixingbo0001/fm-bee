<?php

//单元测试的初始化文件

$dir = dirname(realpath(__DIR__));

include $dir . "/vendor/autoload.php";

$app = new \Core\Application($dir);

return $app;