<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/29
 * Time: 上午9:54
 */

namespace Core\Config;


use Ddup\Part\Libs\Str;

class Config
{

    public static function load()
    {
        return self::isCached() ? self::getFromCache() : self::getFromFile();
    }

    public static function files($dir)
    {
        $files = scandir($dir);

        $reuslt = [];

        foreach ($files as $file) {

            $path = $dir . '/' . $file;

            if (is_file($path)) {
                $reuslt[] = $path;
            }
        }

        return $reuslt;
    }

    public static function push(&$config, $file)
    {
        $name = Str::first(basename($file), '.');

        $arr = [
            $name => include $file
        ];

        $config = array_merge($config, $arr);
    }

    public static function cachePrepare()
    {
        $cache_file = self::getCacheFilePath();
        $cache_dir  = dirname($cache_file);

        is_dir($cache_dir) || mkdir($cache_dir, 0777, true);

        is_file($cache_file) || file_put_contents($cache_file, '<?php return [];');
    }

    public static function getCacheFilePath()
    {
        return bootstrapPath('cache/config.php');
    }

    public static function getFromFile()
    {
        $dir = configPath();

        $files = self::files($dir);

        $config = [];

        foreach ($files as $file) {
            self::push($config, $file);
        }

        return $config;
    }

    public static function getFromCache()
    {
        $file = self::getCacheFilePath();

        return is_file($file) ? require $file : [];
    }

    public static function isCached()
    {
        $file = self::getCacheFilePath();

        return is_file($file);
    }

    public static function cache($config)
    {
        self::cachePrepare();

        file_put_contents(self::getCacheFilePath(), self::format($config));
    }

    public static function format($config)
    {
        return "<?php \r\n return \r\n" . var_export($config, true) . ';';
    }
}