<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/13
 * Time: 下午2:54
 */

namespace Core\Job;


use Core\Job\Queue\Event;

class Dispatcher
{

    private $eventer;
    private $client;

    public function __construct(Eventer $eventer)
    {
        $this->eventer = $eventer;
        $this->client  = new Event();
    }

    public function dispatch()
    {
        return $this->client->create($this->eventer->serilaze());
    }
}