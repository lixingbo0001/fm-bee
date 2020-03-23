<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 上午11:11
 */

namespace App\Facades;


use Core\Facade\Facade;
use Core\Route\RouteItem;

/**
 * @method static RouteItem any($uri, $controller, $action)
 * @method static RouteItem get($uri, $controller, $action)
 * @method static RouteItem post($uri, $controller, $action)
 * @method static RouteItem delete($uri, $controller, $action)
 * @method static RouteItem put($uri, $controller, $action)
 * @method static RouteItem options($uri, $controller, $action)
 * @method static RouteItem head($uri, $controller, $action)
 * @method static RouteItem patch($uri, $controller, $action)
 */
class Route extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}