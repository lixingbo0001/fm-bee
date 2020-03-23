<?php

namespace Core\Queue\Nsq;


use Ddup\Part\Struct\StructReadable;

class Config extends StructReadable
{
    public $host;
    public $transto_second = false;
    public $listen;
    public $namespace;
    public $channel;
    public $topics         = [];
}