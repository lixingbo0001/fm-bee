<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/12/16
 * Time: 下午2:45
 */

namespace Core\Job\Queue;


class Event extends BaseService
{

    public function __construct()
    {
        parent::__construct('event');
    }

    public function create($body)
    {
        return $this->client->post($this->config->endpoint, compact('body'));
    }
}