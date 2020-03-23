<?php

namespace Core;

use Core\Config\Config;
use Core\Container\ServiceProvider;
use Core\Contracts\MiddlewareInterface;
use Core\Exceptions\Contracts\ExceptionHandler;
use Core\Exceptions\ExceptionApplication;
use Core\Exceptions\ExceptionHanlder;
use Core\Facade\Facade;
use Core\Config\Repository;
use Core\Container\Container;
use Illuminate\Support\Arr;


class Application extends Container
{


    const VERSION = "0.0.1";

    private $_has_been_bootstrapped = false;

    private $_paths = [
        'path.base'      => '',
        'path.app'       => 'app',
        'path.config'    => 'config',
        'path.bootstrap' => 'bootstrap',
        'path.route'     => '',
        'path.lang'      => 'bootstrap' . DIRECTORY_SEPARATOR . 'lang',
    ];

    protected $_providers   = [];
    protected $_middlewares = [];

    protected $_bootstraps = [
        \Core\Bootstrap\HandleExceptions::class
    ];

    static function app():self
    {
        return self::$instance;
    }

    function __construct($basePath = null)
    {
        static::$instance = $this;

        // 1. 基础组件
        $this->registerBaseBindings();
        // 2. 设置 path
        $this->setPath($basePath);
        // 3. 加载 config
        $this->loadConfig();
        // 4. 初始化
        $this->bootstrap();
    }

    function version()
    {
        return self::VERSION;
    }

    public function setPath($path)
    {
        foreach ($this->_paths as $name => $dir) {
            $this->instance($name, $path . ($dir ? DIRECTORY_SEPARATOR . $dir : ''));
        }

        return $this;
    }

    private function baseProvider()
    {
        foreach ($this->_providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    public function registerProvider($class)
    {
        $provider = new $class($this);

        if (!($provider instanceof ServiceProvider)) {
            throw new ExceptionApplication(printf("[%s] must instanceof [%s]", $class, ServiceProvider::class));
        }

        $provider->register();

        $provider->bootstrap();
    }

    private function loadConfig()
    {
        $this->_providers = $this->config->get('app.providers');
    }

    private function registerBaseBindings()
    {
        self::bind(ExceptionHandler::class, function () {
            return new ExceptionHanlder();
        });

        self::bind('config', function () {
            return new Repository(Config::load());
        });
    }

    public function bootstrap()
    {
        $this->bootstrapWith($this->_bootstraps);
        $this->baseProvider();

        Facade::setFacadeApplication($this);
    }

    public function registerProviderMap($providers)
    {
        foreach ($providers as $class) {
            $this->registerProvider($class);
        }
    }

    public function pushMiddleware($middlewares)
    {
        $this->_middlewares = array_merge($this->_middlewares, $middlewares);
    }

    function handle($handle, ... $params)
    {
        return $this->$handle->handle(... $params);
    }

    function bootstrapWith($bootstrappers)
    {
        $this->_has_been_bootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }
    }

    function getMiddlewares()
    {
        return $this->_middlewares;
    }

    /**
     * @param $name
     * @return MiddlewareInterface
     * @throws ExceptionApplication
     */
    function getMiddleware($name)
    {
        if (!array_key_exists($name, $this->_middlewares)) {
            throw new ExceptionApplication(sprintf("[%s] middleware not exits", $name));
        }

        $middleware = new $this->_middlewares[$name];

        if (!($middleware instanceof MiddlewareInterface)) {
            throw new ExceptionApplication(sprintf("[%s] must instanceof [%s]", MiddlewareInterface::class));
        }

        if ($this->offsetExists('middleware.' . $name)) {
            return $this->get('middleware.' . $name);
        }

        $this->bind('middleware.' . $name, function () use ($middleware) {
            return $middleware;
        }, true);

        return $middleware;
    }

    function runningInConsole()
    {
        return Arr::get($_ENV, 'APP_RUNNING_IN_CONSOLE') === 'true' || php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }
}