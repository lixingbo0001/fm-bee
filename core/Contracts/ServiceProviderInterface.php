<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 上午11:27
 */

namespace Core\Contracts;


interface ServiceProviderInterface
{
    function register();

    function bootstrap();
}