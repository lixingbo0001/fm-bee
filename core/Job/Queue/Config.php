<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/16
 * Time: 下午2:45
 */

namespace Core\Job\Queue;


use Ddup\Part\Struct\StructReadable;

class Config extends StructReadable
{

    public $domain;
    public $endpoint;
    public $timeout;
    public $method;
}