<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 下午2:58
 */

namespace App\Http\Middlewares;


use Core\Request\Request;

class TestMiddleware extends BaseMiddleware
{
    function before(Request $request)
    {
        $request->offsetSet('middleware', __CLASS__);

        dump(__FUNCTION__);
    }

    function after($response)
    {
        dump(__FUNCTION__);

        return $response;
    }
}