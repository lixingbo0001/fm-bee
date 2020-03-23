<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/24
 * Time: 上午9:42
 */

namespace Core\Controller\Traits;


trait ActionInfoTrait
{
    private $controller;
    private $action;

    public function setAction($action)
    {
        $this->action     = $action;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getController()
    {
        return $this->controller;
    }
}