<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/13
 * Time: 下午2:54
 */

namespace Core\Job;


use App\Exceptions\ExceptionCustomCodeAble;
use Core\Job\Queue\ApiResult;
use Core\Job\Queue\Client;
use Core\Job\Queue\Config;
use Ddup\Part\Api\ApiResultInterface;
use Ddup\Part\Api\ApiResulTrait;

class BaseService
{
    use ApiResulTrait;

    protected $config;
    protected $client;
    protected $throwable = true;

    public function __construct($prefix)
    {
        $this->config = new Config(config('job.' . $prefix));
        $this->client = new Client($this->config);
    }

    function newResult($ret):ApiResultInterface
    {
        return new ApiResult($ret);
    }

    public function request($method, $endpoint, $data)
    {
        $ret = $this->client->request($method, $endpoint, $data);

        $result = $this->parseResult($ret);

        if (!$result->isSuccess()) {
            throw new ExceptionCustomCodeAble("job 服务:" . $result->getMsg());
        }

        return $result;
    }
}