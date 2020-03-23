<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 下午4:43
 */

namespace Core\Container;


use Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends \Exception implements NotFoundExceptionInterface
{

}