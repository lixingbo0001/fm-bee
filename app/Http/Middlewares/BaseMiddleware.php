<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 下午2:58
 */

namespace App\Http\Middlewares;


use App\Exceptions\ExceptionCustomCodeAble;
use Core\Contracts\MiddlewareInterface;
use Core\Contracts\ResponseInterface;

abstract class BaseMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    public function getResponse()
    {
        if (!$this->response) throw new ExceptionCustomCodeAble('中间件退出需返回response');

        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }
}