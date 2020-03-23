<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/28
 * Time: 下午4:49
 */

namespace Core\Database;


use Core\Application;
use Core\Database\Query\Exception\SqlException;
use \PDO;
use InvalidArgumentException;


class Medoo
{

    private $_app;
    private $_query_string;
    private $db;

    protected $type;

    protected $prefix;

    protected $statement;

    public function __construct(array $options, Application $app)
    {
        $this->_app = $app;

        $this->db = new \Medoo\Medoo($options);
    }

    protected function columnQuote($string)
    {
        if (!preg_match('/^[a-zA-Z0-9_]+(\.?[a-zA-Z0-9_]+)?$/i', $string)) {
            throw new InvalidArgumentException("Incorrect column name \"$string\"");
        }

        if (strpos($string, '.') !== false) {
            return '"' . $this->prefix . str_replace('.', '"."', $string) . '"';
        }

        return '"' . $string . '"';
    }

    protected function tableQuote($table)
    {
        return '"' . $this->prefix . $table . '"';
    }

    protected function typeMap($value, $type)
    {
        $map = [
            'NULL'     => PDO::PARAM_NULL,
            'integer'  => PDO::PARAM_INT,
            'double'   => PDO::PARAM_STR,
            'boolean'  => PDO::PARAM_BOOL,
            'string'   => PDO::PARAM_STR,
            'object'   => PDO::PARAM_STR,
            'resource' => PDO::PARAM_LOB
        ];

        if ($type === 'boolean') {
            $value = ($value ? '1' : '0');
        } elseif ($type === 'NULL') {
            $value = null;
        }

        return [$value, $map[$type]];
    }

    protected function buildRaw($raw, &$map)
    {
        $query = preg_replace_callback(
            '/((FROM|TABLE|INTO|UPDATE)\s*)?\<([a-zA-Z0-9_\.]+)\>/i',
            function ($matches) {
                if (!empty($matches[2])) {
                    return $matches[2] . ' ' . $this->tableQuote($matches[3]);
                }

                return $this->columnQuote($matches[3]);
            },
            $raw->value);

        $raw_map = $raw->map;

        if (!empty($raw_map)) {
            foreach ($raw_map as $key => $value) {
                $map[$key] = $this->typeMap($value, gettype($value));
            }
        }

        return $query;
    }

    public function exec($sql, $map)
    {
        $oldMap = $map;

        $raw = $this->db->raw($sql, $map);

        $this->buildRaw($raw, $map);

        try {
            $result = $this->db->exec($sql, $map);

        } catch (\Exception $e) {

            throw new SqlException($e->getMessage() . "\n sql " . $sql . ' param :' . json_encode($oldMap));
        }

        $this->_query_string = $result->queryString;

        if ($result->errorCode() !== '00000') {

            throw new SqlException(join(':', $this->db->error()) . "\n sql: " . $sql . ' param :' . json_encode($oldMap));
        }

        return $result;
    }

    private function aggregate($sql, $map)
    {
        $query  = $this->exec($sql, $map);
        $number = $query->fetchColumn();

        return is_numeric($number) ? $number + 0 : $number;
    }

    public function lastId()
    {
        return $this->db->id();
    }

    public function count($sql, $map)
    {
        return $this->aggregate($sql, $map);
    }

    public function insert($sql, $map)
    {
        return $this->exec($sql, $map);
    }

    public function update($sql, $map)
    {
        return $this->exec($sql, $map);
    }

    public function delete($sql, $map)
    {
        return $this->exec($sql, $map);
    }

    public function fetch($sql, $map = [])
    {
        $result = [];

        $query = $this->exec($sql, $map);

        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $data;
        }

        return $result;
    }

    public function getSql()
    {
        return $this->_query_string;
    }
}