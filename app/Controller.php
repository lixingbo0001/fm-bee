<?php

namespace App;

use Core\Controller\Contracts\ActionInfoInterface;
use Core\Controller\Traits\ActionInfoTrait;
use Core\Application;
use Core\Controller\Traits\ResponseTrait;
use Core\Controller\Traits\ValidateTrait;

class Controller implements ActionInfoInterface
{
    use ActionInfoTrait, ValidateTrait, ResponseTrait;

    public function __construct()
    {
        $this->__before();
    }

    public function __before()
    {
    }

    protected function app()
    {
        return Application::app();
    }

    protected function request()
    {
        return request();
    }
}