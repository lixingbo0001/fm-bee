<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:14
 */

namespace Core\Console\Commands;


use Core\Config\Config as ConfigHelper;
use Core\Console\Command;


class ConfigCache extends Command
{
    protected $signature = 'config:cache';

    protected $description = "缓存配置文件";

    public function handle()
    {
        ConfigHelper::cache(ConfigHelper::getFromFile());
    }
}