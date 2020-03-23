<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/14
 * Time: 上午10:32
 */

namespace Core\Route;


use Core\Route\Contracts\RouteMagicInterface;

class Group implements RouteMagicInterface
{

    function where($name, $pattern)
    {
        return $this;
    }

    function middleware($name)
    {
        return $this;
    }

    function name($name)
    {
        return $this;
    }


}