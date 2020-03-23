<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/24
 * Time: 下午12:06
 */

namespace App\Exceptions;



class ValidateException extends \Exception
{

    public function __construct(\Illuminate\Contracts\Validation\Validator $validator, ?\Symfony\Component\HttpFoundation\Response $response = null, string $errorBag = 'default')
    {
        $this->response  = $response;
        $this->errorBag  = $errorBag;
        $this->validator = $validator;

        parent::__construct($validator->getMessageBag()->first());
    }
}