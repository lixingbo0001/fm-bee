<?php

namespace Core\Controller\Traits;

trait ResponseTrait
{
    function response($ret, $op_name = '操作', $data = [])
    {
        return $ret ? self::success($op_name . '成功', $data) : self::error($op_name . '失败', 'fail');
    }

    function success($msg = '', $data = [])
    {
        if (!is_string($msg)) {
            $data = $msg;
            $msg  = 'OK';
        }

        return myResponse()->success()->msg($msg)->data($data);
    }

    function error($msg = '', $code = 'fail')
    {
        $r = myResponse()->fail();

        if (!is_null($code)) {
            $r = $r->code($code);
        }

        return $r->msg($msg);
    }
}
