<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2020/3/23
 * Time: 下午6:46
 */

namespace Core\Queue;


use Ddup\Part\Api\ApiResultInterface;

interface QueueInterface
{
    /**
     * @param $queue
     * @param $message
     * @param int $defer
     * @return ApiResultInterface
     */
    function pub($queue, $message, $defer = 0);

    function sub($queue);
}