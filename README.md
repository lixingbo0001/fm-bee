## 1. 仿laravel小型框架

### 1.1 重要配置文件

配置文件统一放在更目录config下面

项目启动需要先配置这两个配置文件

app.php

```php
<?php

return [
    'app_env'   => 'dev', //必须
    'providers' => [
        'Providers\ExampleProvider' //非必须
    ],
    'dump'      => [
        'open'   => true,//false的时候，在dump函数不会执行
        'safety' => true,//安全模式下，dump会写入server，浏览器端将看不到内容
        'host'   => 'tcp://127.0.0.1:9912',//dumpServer服务地址
    ]
];
```



### 1.2 其他配置


queue.php

```php
<?php

return [
   "driver" => "nsq",
   "nsq" => [
       'namespace' => '\\App\\Script',//执行任务脚本的命名空间
       'channel'   => 'blue',
       'host'           => 'http://127.0.0.1:4151',//队列接收器地址
       'listen'         => '127.0.0.1:4150',//队列处理器地址
       'transto_second' => true,//使用队列的时候是否将defer转换成秒
   ]
];
```

database.php

```php
<?php

return [
    'mysql' => [
        'database_type' => 'mysql',
        'database_name' => '{dbname}',
        'server'        => '{host}',
        'username'      => '{user}',
        'password'      => '{password}',
        'charset'       => 'utf8mb4',
        'port'          => 3306,
        'logging'       => false,//开启后,执行的sql语句会写入日志
    ],
];
```


