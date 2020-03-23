<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/14
 * Time: 上午10:32
 */

namespace Core\Route;

use Core\Application;
use Core\Route\Struct\MatchedStruct;
use Ddup\Part\Libs\Str;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    private $_name;
    /**
     * @var  RouteCollection
     */
    private $_collection;
    /**
     * @var RouteItem
     */
    private $route_item;
    private $_controller;
    private $_action;
    /**
     * @var MatchedStruct
     */
    private $_matched;

    private const methods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    public function __construct(Application $application)
    {
        $this->_collection = new RouteCollection();
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    private function createRoute($uri, $methods)
    {
        $methods = (array)$methods;

        $this->_name = $uri . "@" . join($methods, '.');

        $this->route_item = $route = new RouteItem($this, $uri);

        $route->addDefaults([
            '_controller' => $this->_controller . "@" . $this->_action
        ]);

        $route->setMethods($methods);

        $this->_collection->add($this->_name, $route->getRoute());

        return $route;
    }

    public function parse()
    {
        $path = $this->_matched->_route_object->getDefault('_controller');

        $class = Str::first($path, '@');

        $action = Str::last($path, '@');

        return [$class, $action];
    }

    private function getRequestContext(\Core\Request\Request $request)
    {
        $baseUrl     = $request->server('REQUEST_SCHEME') . ":" . $request->server('SERVER_PORT') . '//' . $request->server('SERVER_NAME');
        $method      = $request->server('REQUEST_METHOD');
        $host        = $request->server('HTTP_HOST');
        $scheme      = $request->server('REQUEST_SCHEME');
        $path        = $request->server('PATH_INFO');
        $queryString = $request->server('QUERY_STRING');

        return new RequestContext($baseUrl, $method, $host, $scheme, 80, 443, $path, $queryString);
    }

    public function match()
    {
        $request = request();

        $matcher = new UrlMatcher($this->_collection, $this->getRequestContext($request));

        $this->_matched = new MatchedStruct($matcher->match($request->getPathInfo()));

        $this->_matched->_route_object = $route = $this->_collection->get($this->_matched->_route);

        return $this->_matched;
    }

    public function name($name)
    {
        $this->_collection->remove($this->_name);

        $this->_name = $name;

        $this->_collection->add($this->_name, $this->route_item->getRoute());

        return $this;
    }

    public function any($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, self::methods);
    }

    public function get($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'GET');
    }

    public function post($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'POST');
    }

    public function delete($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'DELETE');
    }

    public function put($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'PUT');
    }

    public function options($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'OPTIONS');
    }

    public function patch($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'PATCH');
    }

    public function head($uri, $controller, $action)
    {
        list($this->_controller, $this->_action) = [$controller, $action];

        return $this->createRoute($uri, 'HEAD');
    }
}