<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/26
 * Time: 下午5:21
 */

namespace App\Script;

use App\Script\Autocall\Callback;
use Core\Serve\Nsq\Publish\PublishInterface;
use Ddup\Part\Conditions\Equal;
use Ddup\Part\Conditions\Greater;
use Ddup\Part\Conditions\Less;
use Ddup\Part\Conditions\NotEqual;
use Ddup\Part\Conditions\Preg;
use Ddup\Part\Contracts\ConditionContract;
use Ddup\Part\Message\MsgFromArray;
use App\Script\Autocall\MessageModel;
use Core\Request\Http\HttpClient;
use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\OutCliColor;

class Autocall
{
    /**
     * @var PublishInterface
     */
    private $_publish;
    private $_topic = 'autocall';

    public function handle(MessageModel $message)
    {
        $this->_publish = app('Core\Queue\QueueInterface');

        $client = new HttpClient($message->request_url);

        OutCli::printLn("最大次数:" . $message->max_execute . ", 当前执行次数:" . ($message->execute_index), OutCliColor::green());

        $response = $client->{$message->request_method}($message->request_url, $message->body);

        OutCli::printLn($response, OutCliColor::green());

        if (!$this->isCondition($message, $response)) {
            $this->rollbackToNext($this->_topic, $message);
            return;
        }

        $this->callback($message, $response);
    }

    private function isCondition(MessageModel $data, $response)
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

    private function callback(MessageModel $data, $response)
    {
        if (is_null($response)) {
            return false;
        }

        if (!$this->isCondition($data, $response)) {
            return false;
        }

        return Callback::do($data, $response);
    }

    private function rollbackToNext($topic, MessageModel $data)
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