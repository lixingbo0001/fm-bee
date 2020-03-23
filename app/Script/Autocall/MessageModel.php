<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/27
 * Time: 上午10:22
 */

namespace App\Script\Autocall;


use Ddup\Part\Struct\StructReadable;

class MessageModel extends StructReadable
{
    public $execute_time;
    public $body;
    public $callback;
    public $request_url;
    public $request_method;
    public $condition;
    public $next_delay;
    public $max_execute;
    public $execute_index = 1;
}