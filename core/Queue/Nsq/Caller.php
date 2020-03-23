<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/26
 * Time: 下午4:56
 */

namespace Core\Queue\Nsq;


use App\Script\Autocall\MessageModel;
use Core\Queue\QueueInterface;
use Ddup\Part\Message\MsgFromArray;
use Ddup\Part\Conditions\Equal;
use Ddup\Part\Conditions\Greater;
use Ddup\Part\Conditions\Less;
use Ddup\Part\Conditions\NotEqual;
use Ddup\Part\Conditions\Preg;
use Ddup\Part\Contracts\ConditionContract;

class Caller
{

    private $_publish;

    public function __construct(QueueInterface $publish)
    {
        $this->_publish = $publish;
    }

    public function isCondition(MessageModel $data, $response)
    {
        $response  = new MsgFromArray($response);
        $condition = $data->condition ?: [];

        $type = array_get($condition, 'type');
        $key  = array_get($condition, 'key');
        $val  = array_get($condition, 'val');

        $matchers = [
            new Equal(),
            new Greater(),
            new Less(),
            new NotEqual(),
            new Preg()
        ];

        foreach ($matchers as $matcher) {
            if ($matcher instanceof ConditionContract) {

                if ($matcher->getName() != $type) continue;

                return $matcher->matched($response, $key, $val);
            }
        }

        return false;
    }

    public function callback(MessageModel $data, $response, $callback)
    {
        if (is_null($response)) {
            return false;
        }

        if (!$this->isCondition($data, $response)) {
            return false;
        }

        return $callback($data, $response);
    }

    public function rollbackToNext($topic, MessageModel $data)
    {
        $data->execute_index = (int)$data->execute_index;

        if ($data->execute_index >= $data->max_execute) {
            return;
        }

        $data->execute_index++;

        $this->_publish->pub($topic, $data->toArray(), $this->nextStepDelay($data));
    }

    private function nextStepDelay(MessageModel $data)
    {
        $next_delay = (int)$data->next_delay;

        $next_delay = max($next_delay, 1);

        return $next_delay;
    }
}

