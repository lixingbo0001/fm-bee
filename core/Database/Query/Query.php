<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/21
 * Time: 下午5:35
 */

namespace Core\Database\Query;


class Query
{


    private $table;

    public $alias;
    public $columns = [];
    public $where   = [];
    public $limit;
    public $offset;
    public $join;
    public $order;
    public $data;

    public $binding = [
        'where'  => [],
        'update' => [],
        'order'  => [],
    ];

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function clear()
    {
        $this->binding = [
            'where'  => [],
            'update' => [],
            'order'  => []
        ];

        $this->order  = [];
        $this->where  = [];
        $this->alias  = null;
        $this->limit  = null;
        $this->offset = null;
        $this->join   = null;
        $this->data   = null;
    }

    private function dataFilter($data)
    {
        $result = [];

        foreach ($data as $k => $v) {
            if (!in_array($k, $this->columns)) {
                continue;
            }

            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * @desc 获取表名
     * @return mixed
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * @desc 表别名
     * @param $alias
     * @return $this
     */
    public function alias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    static function isBatchDataInsert($data)
    {
        return count($data) != count($data, CASE_UPPER);
    }

    public function setData($data)
    {
        //批量插入不过滤字段
        if (self::isBatchDataInsert($data)) {
            //$this->data = $data;这种做法错误，不能保证数组的index是从0开始，依次递增

            foreach ($data as $datum) {
                $this->data[] = $datum;
                $this->addBinding($datum, 'update');
            }
        } else {
            $this->data = $this->dataFilter($data);

            $this->addBinding($this->data, 'update');
        }
    }

    /**
     * @desc 表关联
     * @param $table
     * @param $local
     * @param $foregin
     * @param string $join
     * @return $this
     */
    public function join($table, $local, $foregin, $join = 'inner')
    {
        $this->join[] = [
            'type'    => $join,
            'table'   => $table,
            'local'   => $local,
            'foregin' => $foregin
        ];

        return $this;
    }

    /**
     * @desc 排序
     * @param $name
     * @param string $sort
     * @return $this
     */
    public function orderBy($name, $sort)
    {
        $this->order[] = [$name => $sort];

        return $this;
    }

    /**
     * @desc 降序
     * @param $name
     * @return $this
     */
    public function orderDesc($name)
    {
        $this->orderBy($name, 'DESC');

        return $this;
    }

    /**
     * @desc 查询字段
     * @param $names
     * @return $this
     */
    public function select($names)
    {
        $this->columns = func_num_args() == 1 ? (array)$names : func_get_args();

        return $this;
    }

    /**
     * @desc 查询字段
     * @param $names
     * @return $this
     */
    public function addSelect($names)
    {
        $this->columns = array_merge($this->columns, func_num_args() == 1 ? (array)$names : func_get_args());

        $this->columns = array_unique($this->columns);

        return $this;
    }

    /**
     * @desc 预绑定参数设置
     * @param $value
     * @param string $type
     * @return $this
     */
    public function addBinding($value, $type = 'where')
    {
        if (is_array($value)) {
            $this->binding[$type] = array_values(array_merge($this->binding[$type], $value));
        } else {
            $this->binding[$type][] = $value;
        }

        return $this;
    }

    /**
     * @example
     *
     * where('id', 1)->
     * where(['name' => 'blue'])
     *
     * sql : where id = 1 and name = "blue"
     *
     * @param $conditions
     * @param $operator
     * @param $type
     * @return $this
     */
    public function where($conditions, $operator = 'eq', $type = 'and')
    {
        foreach ($conditions as $name => $value) {

            if ($operator == '~' || $operator == 'like') {
                $value = "%{$value}%";
            }

            $this->where[] = [
                'type'     => $type,
                'name'     => $name,
                'operator' => $operator,
                'value'    => $value,
            ];

            $this->addBinding($value, 'where');
        }

        return $this;
    }

    public function range($offset, $limit)
    {
        $this->offset($offset);
        $this->limit($limit);

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }
}