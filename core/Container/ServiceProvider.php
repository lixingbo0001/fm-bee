<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/12
 * Time: 下午1:46
 */

namespace Core\Container;


use Core\Application;
use Core\Console\Console;

abstract class ServiceProvider
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function register();

    abstract public function bootstrap();

    /**
     * Register the package's custom Artisan commands.
     *
     * @param  array|mixed $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Console::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }
}
