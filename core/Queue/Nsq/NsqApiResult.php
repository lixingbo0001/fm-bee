<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/22
 * Time: 下午6:23
 */

namespace Core\Queue\Nsq;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;

class NsqApiResult implements ApiResultInterface
{

    private $result;
    private $string;

    public function __construct($ret)
    {
        if (!$ret) {
            $ret = '{"err" : "网络错误"}';
        }

        $this->string = $ret;
        $ret          = json_decode($ret, true);
        $this->result = new Collection($ret);
    }

    public function get($name)
    {
    }

    public function getCode()
    {
    }

    public function getData():Collection
    {
        return $this->result;
    }

    public function getMsg()
    {
        return $this->string;
    }

    public function isSuccess()
    {
        return strtoupper($this->string) == 'OK';
    }
}