<?php

namespace Core\Request\Http;


use Ddup\Part\Request\HasHttpRequest;

class HttpClient
{

    use HasHttpRequest;

    private $_host;

    public function __construct($host)
    {
        $this->_host = $host;
    }

    public function setTimeout($timeout)
    {
        if (!is_null($timeout)) {
            $this->timeout = $timeout;
        }
    }

    public function get($url, $query = [], $headers = [])
    {
        return $this->request('get', $url, $query);
    }

    public function post($url, $data, $options = [])
    {
        return $this->request('post', $url, $data);
    }

    public function request($method, $url, $data = [])
    {
        $options = [];

        switch ($method) {
            case 'get':
                $options = [
                    'query' => $data,
                ];
                break;
            case 'post':
                if (!is_array($data)) {
                    $options['body'] = $data;
                } else {
                    $options['form_params'] = $data;
                }

                break;
        }

        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($url, $options));
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


}