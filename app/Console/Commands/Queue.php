<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:14
 */

namespace App\Console\Commands;


use Core\Console\Command;
use Core\Queue\QueueInterface;


class Queue extends Command
{
    protected $signature   = 'queue:test';
    protected $description = "测试队列";

    /**
     * @return QueueInterface
     */
    private function queue()
    {
        return $this->app->get(QueueInterface::class);
    }

    public function handle()
    {
        $this->queue()->pub("autocall", [
            "method" => "a"
        ]);
    }

}