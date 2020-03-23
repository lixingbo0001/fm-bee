<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/17
 * Time: 下午5:05
 */

namespace Core\Job\Test;


use Core\Job\Eventer;
use PHPUnit\Framework\TestCase;

class DeleteEvent extends Eventer
{

    public function test_dispatch()
    {
        $message = [
            'project_id' => 1,
            'id'         => 1
        ];
    }
}