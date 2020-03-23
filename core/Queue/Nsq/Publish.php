<?php

namespace Core\Queue\Nsq;


use Core\Queue\QueueInterface;

class Publish implements QueueInterface
{

    private $config;
    private $_client;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->_client = new Client($config->host);
    }

    public function delay($defer, $auto_second = false)
    {
        $defer = intval($defer);

        if ($auto_second) {
            $defer *= 1000;
        }

        return $defer;
    }

    public function pub($topic, $message, $delay = 0)
    {
        $delay = $this->delay($delay, $this->config->transto_second);

        $query = [
            'topic' => $topic
        ];

        if ($delay) {
            $query['defer'] = $delay;
        }

        return $this->_client->json('pub', $message, $query);
    }

    function sub($queue)
    {
    }


}