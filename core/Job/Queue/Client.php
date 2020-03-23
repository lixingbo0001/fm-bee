<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/16
 * Time: 下午2:45
 */

namespace Core\Job\Queue;


use Ddup\Part\Request\HasHttpRequest;

class Client
{
    use HasHttpRequest;

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    function getBaseUri()
    {
        return $this->config->domain;
    }

    function requestOptions()
    {
        return [];
    }

    function requestParams()
    {
        return [];
    }



}