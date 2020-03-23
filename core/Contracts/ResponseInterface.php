<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 上午11:27
 */

namespace Core\Contracts;


interface ResponseInterface
{
    function data($data):self;

    function code($code):self;

    function success():self;

    function isSuccess():self;

    function fail():self;

    function message($msg):self;

    function msg($msg):self;

    public function setContent($content):self;

    public function extends(array $param):self;
}