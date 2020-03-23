<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:14
 */

namespace App\Console\Commands;


use Core\Console\Command;


class Runtime extends Command
{
    protected $signature   = 'runtime:test {times}';
    protected $description = "测试运行时间";

    private $_times;

    public function handle()
    {
        $this->_times = $this->input->getArgument('times');

        $t1 = $this->testClass1();

        $t2 = $this->testClass2();

        $this->info("变量运行时间" . $t1);

        $this->info("普通运行时间" . $t2);

        $this->error("相差时间" . ($t2 - $t1));
    }

    private function testClass1()
    {
        $s = microtime(true);

        for ($i = 0; $i < $this->_times; $i++) {
            $this->newClass1();
        }

        $e = microtime(true);

        return $e - $s;
    }

    private function testClass2()
    {
        $s = microtime(true);

        for ($i = 0; $i < $this->_times; $i++) {
            $this->newClass2();
        }

        $e = microtime(true);

        return $e - $s;
    }

    private function newClass1()
    {
        $class = __NAMESPACE__ . "\\Test";

        return new $class();
    }

    private function newClass2()
    {
        return new Test();
    }
}