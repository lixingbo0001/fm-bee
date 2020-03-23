<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 上午11:04
 */

namespace Core\Serve\Http;


use Core\Container\ServiceProvider;
use Core\Route\Router;
use Ddup\Part\Response\Response;

class HttpProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('router', function ($app) {
            return new Router($app);
        }, true);

        $this->app->bind('response', function () {
            return new Response();
        });
    }

    public function bootstrap()
    {
    }
}

