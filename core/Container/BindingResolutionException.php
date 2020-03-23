<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/15
 * Time: 下午4:34
 */

namespace Core\Container;

use Psr\Container\ContainerExceptionInterface;

class BindingResolutionException extends \Exception implements ContainerExceptionInterface
{
}