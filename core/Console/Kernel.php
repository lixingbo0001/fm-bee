<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:46
 */

namespace Core\Console;

use Core\Application;
use Core\Console\Console as Artisan;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

abstract class Kernel
{

    private   $artisan;
    private   $app;
    private   $commandsLoaded = false;
    protected $commands       = [];

    abstract function commands();

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($input, $output)
    {
        $this->bootstrap();

        return $this->getArtisan()->run($input, $output);
    }

    public function bootstrap()
    {
        if (!$this->commandsLoaded) {

            $this->load(__DIR__ . '/Commands');

            $this->commands();

            $this->commandsLoaded = true;
        }
    }

    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new Artisan($this->app))
                ->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

    /**
     * 加载自定义的命令
     * @param $paths
     * @throws \ReflectionException
     */
    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = '';

        foreach ((new Finder())->in($paths)->files() as $command) {

            $command = $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($command->getPathname(), app('path.base') . DIRECTORY_SEPARATOR)
                );

            $command = ucfirst($command);

            if (is_subclass_of($command, Command::class) && !(new \ReflectionClass($command))->isAbstract()) {
                Artisan::starting(function ($artisan) use ($command) {
                    $artisan->resolve($command);
                });
            }
        }
    }
}
