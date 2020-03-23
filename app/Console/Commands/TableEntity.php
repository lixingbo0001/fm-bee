<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:14
 */

namespace App\Console\Commands;


use Core\Console\Command;


class TableEntity extends Command
{
    protected $signature   = 'table:var {table}';
    protected $description = "将表字段转换为entity属性";

    /**
     * @var \Medoo\Medoo
     */
    private $db;

    public function handle()
    {
        $this->db = app('database.connecting');

        $table = $this->argument('table');

        $result = $this->db->query("SHOW FULL COLUMNS FROM `$table`");

        if ($result->errorCode() != '0000') {
            $this->info("sql:" . $result->queryString);
            $this->error(json_encode($result->errorInfo()));
            return;
        }

        $reday_vars = array_map(function ($v) {
            return "public $" . $v;
        }, array_column($result->fetchAll(), 'Field'));

        echo "\n\n\n";

        echo (join(";\n", $reday_vars)) . ";\n";

        echo "\n\n\n";
    }

}