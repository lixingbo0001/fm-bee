<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/26
 * Time: 下午1:28
 */

namespace Core\DumpServer;


use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\Connection;

class Dumper
{
    private $connection;
    private $safety = false;

    public function __construct(Connection $connection, $safety)
    {
        $this->connection = $connection;
        $this->safety     = $safety;
    }

    /**
     * 非开发环境并且开启了安全模式必须写入server
     * @return bool
     */
    private function mustWriteToServer()
    {
        return !isDev() && $this->safety;
    }

    /**
     * @param $data
     * @return bool true写入成功
     */
    private function tryWriteToServer($data)
    {
        return $this->connection->write($data) !== false;
    }

    /**
     * 写入cli或者html
     * @param $data
     */
    private function writeToClient($data)
    {
        $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new HtmlDumper;
        $dumper->dump($data);
    }

    /**
     * Dump a value with elegance.
     *
     * @param  mixed $value
     * @return void
     */
    public function dump($value)
    {
        $data = (new VarCloner)->cloneVar($value);

        if ($this->mustWriteToServer()) {

            $this->connection->write($data);

            return;
        }

        if (!$this->tryWriteToServer($data)) {//先尝试着写入server，当写入失败的时候直接dump

            $this->writeToClient($data);

            return;
        }
    }
}
