<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/14
 * Time: 下午2:45
 */

namespace Core\Route\Contracts;


interface RouteMagicInterface
{
    /**
     * @param $name
     * @param $pattern
     * @return RouteMagicInterface
     */
    function where($name, $pattern);

    /**
     * @param $name
     * @return RouteMagicInterface
     */
    function middleware($name);

    /**
     * @param $name
     * @return RouteMagicInterface
     */
    function name($name);
}

