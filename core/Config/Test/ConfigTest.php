<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/29
 * Time: 上午10:16
 */

namespace Core\Config\Test;


use Core\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function test_format()
    {
        $arr = [
            "topic" => [
                "order"
            ]
        ];

        $this->assertEquals("<?php \r\n return \r\n" . var_export($arr, true) . ';', Config::format($arr));
    }

    public function test_cache()
    {
        $config = [
            "topic" => [
                "order"
            ]
        ];

        Config::cache($config);

        $arr = require Config::getCacheFilePath();

        $this->assertEquals($arr, $config);
    }

    public function test_makefile()
    {
        $cache_file = Config::getCacheFilePath();

        is_file($cache_file) && unlink($cache_file);

        Config::cache([
            'topic' => [
                'order'
            ]
        ]);

        $this->assertTrue(file_exists($cache_file));
    }

    public function test_scanfiles()
    {
        $files = Config::files(configPath());

        $this->assertGreaterThan(1, count($files));

        foreach ($files as $file){
            $this->assertTrue(is_file($file));
        }
    }

    public function test_push()
    {
        $config = [];

        Config::push($config, __DIR__ . '/data/config.php');

        $this->assertArrayHasKey('config', $config);

        $this->assertEquals('is_test', $config['config']['debug']);
    }
}