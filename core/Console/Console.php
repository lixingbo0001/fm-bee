<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:46
 */

namespace Core\Console;


use Core\Application as App;
use \Closure;

class Console extends \Symfony\Component\Console\Application
{


    private $app;

    protected static $bootstrappers = [];

    public function __construct(App $application)
    {
        parent::__construct('Console', $application->version());

        $this->app = $application;

        $this->bootstrap();
    }

    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        foreach ($commands as $command) {
            $this->resolve($command);
        }

        return $this;
    }

    /**
     * @param $command
     * @return null|\Symfony\Component\Console\Command\Command
     * @throws \Core\Container\BindingResolutionException
     */
    public function resolve($command)
    {
        $instance = $this->app->make($command);

        $instance instanceof Command && $instance->setApp($this->app);

        return $this->add($instance);
    }

    /**
     * 注册命令
     * @param Closure $callback
     */
    public static function starting(Closure $callback)
    {
        static::$bootstrappers[] = $callback;
    }

    /**
     * Bootstrap the console application.
     *
     * @return void
     */
    protected function bootstrap()
    {
        foreach (static::$bootstrappers as $bootstrapper) {
            $bootstrapper($this);
        }
    }
}