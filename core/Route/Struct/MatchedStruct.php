<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/2
 * Time: 下午3:46
 */

namespace Core\Route\Struct;


use Ddup\Part\Struct\StructCompleteReadable;
use Symfony\Component\Routing\Route;

class MatchedStruct extends StructCompleteReadable
{
    public $_route;
    public $_controller;
    /**
     * @var Route
     */
    public $_route_object;
}