<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/13
 * Time: 下午2:54
 */

namespace Core\Job;


use Ddup\Event\EventMessage;

class Eventer
{
    protected $eventName;
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function serilaze()
    {
        $this->content['event_name'] = $this->eventName;

        $message = new EventMessage($this->content);

        return $message->toArray();
    }

    public function dispatch()
    {
        $dispatcher = new Dispatcher($this);

        $dispatcher->dispatch();
    }
}