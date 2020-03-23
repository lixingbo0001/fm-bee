<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/16
 * Time: 下午2:45
 */

namespace Core\Job\Queue;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;

class ApiResult implements ApiResultInterface
{

    private $result;
    private $prefix;

    public function __construct($ret, $prefix = '')
    {
        $this->prefix = $prefix;

        if (!$ret) {
            $ret = '{"retcode" => "fail", "msg" : "网络错误500"}';
        }

        if (is_string($ret)) {
            $ret = json_decode($ret, true);
        }

        $this->result = new Collection($ret);
    }

    function isSuccess()
    {
        return $this->getCode() === 'success';
    }

    function getCode()
    {
        return $this->result->get('retcode');
    }

    function getMsg()
    {
        return $this->prefix . ":" . $this->result->get('msg');
    }

    function getData():Collection
    {
        return new Collection($this->result->get('data'));
    }


}