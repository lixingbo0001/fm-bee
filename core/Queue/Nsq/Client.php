<?php

namespace Core\Queue\Nsq;


use Ddup\Part\Api\ApiResulTrait;
use Ddup\Part\Request\HasHttpRequest;

class Client
{

    use ApiResulTrait;
    use HasHttpRequest;


    private $_host;

    public function __construct($host)
    {
        $this->_host = $host;
    }

    public function newResult($ret)
    {
        return new NsqApiResult($ret);
    }

    function getBaseUri()
    {
        return $this->_host;
    }

    function requestOptions()
    {
        return [];
    }

    function requestParams()
    {
        return [];
    }

    public function request($method, $endpoint, $options = [])
    {
        $options = array_merge($this->requestOptions(), ['handler' => $this->getHandlerStack()], $options);

        $ret = $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options));

        return $this->parseResult($ret);
    }
}