<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/26
 * Time: 下午4:56
 */

namespace Core\Queue\Nsq;


use Core\Application;
use App\Script\Autocall\MessageModel;
use Core\Queue\QueueInterface;
use Illuminate\Support\Str;
use nsqphp\Lookup\FixedHosts;
use nsqphp\Message\MessageInterface;
use nsqphp\nsqphp;


class Listener
{
    private $_publish;
    private $_app;
    private $_config;

    public function __construct(Application $app, Config $config, QueueInterface $publish)
    {
        $this->_app = $app;

        $this->_publish = $publish;

        $this->_config = $config;
    }

    public function run($topics, $errCallback)
    {
        $lookup = new FixedHosts ($this->_config->listen);
        $nsq    = new nsqphp($lookup);

        foreach ($topics as $topic) {
            $nsq->subscribe($topic, $this->_config->channel, function (MessageInterface $msg) use ($topic, $errCallback) {
                try {
                    $this->replayTask($topic, $this->getMessage($msg->getPayload()));
                } catch (\Exception $exception) {
                    $errCallback($exception);
                } catch (\Error $error) {
                    $errCallback($error);
                }
            });
        }

        $nsq->run();
    }

    private function getMessage($payload)
    {
        $msg_data = json_decode($payload, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("nsq message is invalid json");
        }

        return new MessageModel($msg_data);
    }

    private function shouldDo(MessageModel $data)
    {
        return $data->execute_time <= time();
    }

    private function delay(MessageModel $data)
    {
        $delay = max(0, $data->execute_time - time());

        return min($delay, 60 * 30);
    }

    private function replayTask($topic, MessageModel $data)
    {

        if ($this->shouldDo($data)) {

            $this->handle($topic)->handle($data);

            return;
        }

        $this->_publish->pub($topic, $data, $this->delay($data));
    }

    private function handle($topic)
    {
        $listner = self::make($topic);

        return $listner;
    }

    public function make($topic)
    {
        $class = $this->_config->namespace . '\\' . Str::studly($topic);

        $listner = new $class();

        return $listner;
    }
}

