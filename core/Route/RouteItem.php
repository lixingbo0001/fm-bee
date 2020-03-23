<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/14
 * Time: 上午10:32
 */

namespace Core\Route;

use Core\Route\Contracts\RouteMagicInterface;
use \Symfony\Component\Routing\Route as RouteSym;


class RouteItem implements RouteMagicInterface
{
    private $_route_sym;
    private $_router;
    private $_middlewares = [];

    public function __construct(Router $router, $path)
    {
        $this->_router = $router;

        $this->_route_sym = new RouteSym($path);
    }

    public function addDefaults($defaults)
    {
        $this->_route_sym->addDefaults($defaults);
    }

    public function setMethods($methods)
    {
        $this->_route_sym->setMethods($methods);
    }

    public function getRoute()
    {
        return $this->_route_sym;
    }

    public function name($name)
    {
        $this->_router->name($name);

        return $this;
    }

    public function where($name, $partten)
    {
        $this->_route_sym->setRequirement($name, $partten);

        return $this;
    }

    public function middleware($name)
    {
        $this->_middlewares[] = $name;

        $this->_middlewares = array_unique($this->_middlewares);

        $this->_route_sym->setOption('middlewares', $this->_middlewares);

        return $this;
    }
}