<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/12
 * Time: 下午8:11
 */

namespace Core\Serve;


use Core\Application;
use Core\Route\Router;
use Core\Serve\Contracts\ServeHandle;
use Symfony\Component\HttpFoundation\Response;

class HttpHandle implements ServeHandle
{

    /**
     * @var Application
     */
    private $_app;

    private $_providers = [
        'Core\Serve\Http\HttpProvider'
    ];

    public function __construct(Application $app)
    {
        $this->_app = $app;

        $app->registerProviderMap($this->_providers);

        $app->pushMiddleware(config('middleware.http', []));
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        require routePath('route.php');

        return $this->_app->get('router');
    }

    function runWithMiddleware($middleware_names, \Closure $closure)
    {
        $request = request();

        foreach ($middleware_names as $name) {
            $middleware = $this->_app->getMiddleware($name);

            if (false === $middleware->before($request)) {
                return $middleware->getResponse();
            }
        }

        $response = $closure();

        foreach ($middleware_names as $name) {

            $middleware = $this->_app->getMiddleware($name);

            $response = $middleware->after($response);
        }

        return $response;
    }

    private function run()
    {
        $router = $this->getRouter();

        $matched = $router->match();

        $params = array_map(function ($name) use ($matched) {
            return $matched->get($name);
        }, $matched->_route_object->compile()->getVariables());

        $middleware_names = $matched->_route_object->getOption('middlewares') ?: [];

        return $this->runWithMiddleware($middleware_names, function () use ($router, $params) {

            list($controller, $action) = $router->parse();

            $instance = new $controller();

            $instance->setController($controller);
            $instance->setAction($action);
            $instance->validate();

            return $instance->$action(... $params);
        });
    }

    function end($response)
    {
        if (!$response instanceof Response) {
            $response = new Response($response);
        }

        return $response->send();
    }

    function handle()
    {
        $response = $this->run();

        return $this->end($response);
    }

}