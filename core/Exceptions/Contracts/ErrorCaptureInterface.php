<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/25
 * Time: 下午5:13
 */

namespace Core\Exceptions\Contracts;


interface ErrorCaptureInterface
{
    function captureException($e, $data = null, $logger = null, $vars);
}