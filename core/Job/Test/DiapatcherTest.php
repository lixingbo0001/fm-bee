<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/17
 * Time: 下午5:05
 */

namespace Core\Job\Test;


use Core\Job\Test\Provider\DeleteEnvironmentEvent;
use PHPUnit\Framework\TestCase;

class DiapatcherTest extends TestCase
{

    public function test_dispatch()
    {
        $message = [
            'project_id' => 7,
            'id'         => 1
        ];

        (new DeleteEnvironmentEvent($message))->dispatch();

        $this->assertTrue(true);
    }
}