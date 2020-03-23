<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 上午11:25
 */

namespace Providers;

use Core\Container\ServiceProvider;
use Core\Database\Medoo;
use Core\Database\Query\Builder;
use Core\DumpServer\Dumper;
use Core\DumpServer\RequestContextProvider;
use Core\Exceptions\Errs;
use Core\Queue\QueueInterface;
use Core\Request\Request;
use Illuminate\Support\Collection;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Core\Pagination\Paginator;


class AppProvider extends ServiceProvider
{
    function register()
    {
        $this->registerRequest();

        $this->registerQueue();

        $this->registerDb();

        $this->registerPagination();

        $this->setDumpHandle();
    }

    function bootstrap()
    {

    }

    /**
     * 注册request对象
     */
    private function registerRequest()
    {
        $this->app->singleton('request', function () {
            return Request::capture();
        });
    }

    /**
     * 注册队列处理器
     */
    private function registerQueue()
    {
        $driver = config('queue.driver');

        $this->app->singleton('queue.nsq', function () {
            return new \Core\Queue\Nsq\Publish(new \Core\Queue\Nsq\Config(config('queue.nsq')));
        });

        $handleName = 'queue.' . $driver;

        if (!$this->app->offsetExists($handleName)) {
            throw Errs::throwAble("不支持的queue:{$handleName}");
        }

        $this->app->singleton('Core\Queue\QueueInterface', function () use ($handleName) {
            return $this->app->get($handleName);
        });

    }

    /**
     * 注册数据库处理器
     */
    private function registerDb()
    {
        $this->app->singleton('database.connecting', function () {

            return new Medoo(config('database.mysql'), $this->app);
        });

        /**
         * 为了sql查询不用传参
         */
        Builder::registerOnResolver(function (Builder $query) {
            if ($this->app->has('request')) {
                $query->setCollection(new Collection(\request()->toArray()));
            } else {
                $query->setCollection(new Collection());
            }
        });
    }

    /**
     * 接管dump和dd函数的输出
     */
    private function setDumpHandle()
    {
        $dump = config('app.dump');

        if (!$dump['open']) {
            VarDumper::setHandler(function ($var) {
                //什么都不干
            });

            return;
        }

        $host = $dump['host'];

        $connection = new Connection($host, [
            'request' => new RequestContextProvider(\request()),
            'source'  => new SourceContextProvider('utf-8', basePath()),
        ]);

        VarDumper::setHandler(function ($var) use ($connection, $dump) {
            (new Dumper($connection, $dump['safety']))->dump($var);
        });
    }

    /**
     * 注册分页处理器
     */
    private function registerPagination()
    {
        $this->app->bind('paginator', function () {
            $request = request();

            return new Paginator($request->get('page'), $request->get('limit'), 999);
        });
    }

}