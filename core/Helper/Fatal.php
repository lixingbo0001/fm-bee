<?php

namespace Core\Helper;


class Fatal
{

    const color = '1;31';

    public static function printLn($msg)
    {
        $msg = self::format($msg);
        $colorMsg = "\033[" . self::color . "m" . $msg . "\033[0m";
        exit($colorMsg . "\n");
    }

    public static function format($msg)
    {
        if(is_array($msg) || is_object($msg))return json_encode($msg, JSON_UNESCAPED_UNICODE);
        return $msg;
    }
}