<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/20
 * Time: 下午7:41
 */

namespace Core\Database\Query\Exception;


use App\Exceptions\ExceptionCustomCodeAble;

class CurdException extends ExceptionCustomCodeAble
{
    public function __construct(string $message = "", string $code = "", array $row = [])
    {
        $message = "Curd Fail: " . $message;

        parent::__construct($message, $code, $row);
    }
}