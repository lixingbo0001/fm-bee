<?php

namespace App\Exceptions;



use Core\Exceptions\ExceptionHanlder;
use Exception;

class Hanlder extends ExceptionHanlder
{

    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }



}