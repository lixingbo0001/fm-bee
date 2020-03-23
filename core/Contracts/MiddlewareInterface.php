<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 上午11:27
 */

namespace Core\Contracts;


use Core\Request\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @return bool 当为false的时候终止执行
     */
    function before(Request $request);

    function after($response);

    function getResponse();
}