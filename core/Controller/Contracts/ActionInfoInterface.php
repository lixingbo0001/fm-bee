<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/24
 * Time: 上午9:50
 */

namespace Core\Controller\Contracts;


interface ActionInfoInterface
{
    function setAction($action);
    function setController($controller);
    function getAction();
    function getController();
}