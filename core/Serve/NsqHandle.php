<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/12
 * Time: 下午8:11
 */

namespace Core\Serve;


use Core\Application;
use Core\Serve\Contracts\ServeHandle;
use Core\Queue\Nsq\Listener;

class NsqHandle implements ServeHandle
{

    /**
     * @var Application
     */
    private $_app;

    private $_providers = [

    ];

    public function __construct(Application $app)
    {
        $this->_app = $app;

        $app->registerProviderMap($this->_providers);
    }

    function handle()
    {
        $config   = new \Core\Queue\Nsq\Config(config('queue.nsq'));
        $publish  = new \Core\Queue\Nsq\Publish($config);
        $listener = new Listener($this->_app, $config, $publish);

        $listener->run($config->topics, function ($exception) {
            dump($exception);
        });
    }

}