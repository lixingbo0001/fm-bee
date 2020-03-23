<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/3
 * Time: 下午12:11
 */

namespace Core\Exceptions;


use App\Exceptions\ExceptionCustomCodeAble;

class Errs
{
    private $_messsage;

    static function new($message = null)
    {
        $err = new self;

        $err->_messsage = $message;

        return $err;
    }

    static function throwAble($err)
    {
        if ($err instanceof self) {
            $err = $err->string();
        }

        return new ExceptionCustomCodeAble($err, 'error');
    }

    public function string()
    {
        return $this->_messsage;
    }

    public function isError()
    {
        return $this->_messsage != null;
    }

    public function __toString()
    {
        return $this->string();
    }
}