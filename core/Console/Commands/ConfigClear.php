<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:14
 */

namespace Core\Console\Commands;


use Core\Console\Command;


class ConfigClear extends Command
{
    protected $signature = 'config:clear';

    protected $description = "清除配置文件的缓存";

    public function handle()
    {
        if (file_exists(bootstrapPath('cache/config.php'))) unlink(bootstrapPath('cache/config.php'));
    }
}