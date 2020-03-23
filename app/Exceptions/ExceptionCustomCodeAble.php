<?php

namespace App\Exceptions;


class ExceptionCustomCodeAble extends \Exception
{
    public  $row;
    private $customCode;

    public function __construct($message = "", $code = "", Array $row = [])
    {
        $this->row        = $row;
        $this->customCode = $code === "" ? 'fail' : (is_array($code) ? 'array' : $code);
        parent::__construct($message, intval($code));
    }

    public function getCustomCode()
    {
        return $this->customCode;
    }

    public function getRow()
    {
        return $this->row;
    }
}