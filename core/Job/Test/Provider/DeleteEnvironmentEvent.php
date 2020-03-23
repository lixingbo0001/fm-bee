<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/17
 * Time: 下午5:05
 */

namespace Core\Job\Test\Provider;


use Core\Job\Eventer;

class DeleteEnvironmentEvent extends Eventer
{
    protected $eventName = "delete_environment";

}