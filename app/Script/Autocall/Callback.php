<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/27
 * Time: 上午10:22
 */

namespace App\Script\Autocall;


use Core\Request\Http\HttpClient;
use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\OutCliColor;

class Callback
{

    static function do(MessageModel $data, $response)
    {
        $request = new HttpClient($data->callback);

        $callback_response = $request->post('', (array)$response);

        OutCli::printLn('callback:' . $data->callback, OutCliColor::green());

        OutCli::printLn($callback_response, OutCliColor::green());

        return self::isSuccess($callback_response);
    }

    private static function isSuccess($callback_response)
    {
        return is_string($callback_response) && strtoupper($callback_response) == 'SUCCESS';
    }
}

