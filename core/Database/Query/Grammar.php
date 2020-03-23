<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/21
 * Time: 下午8:39
 */

namespace Core\Database\Query;

use Core\Database\Query\Exception\SqlException;
use Illuminate\Support\Arr;

class Grammar
{
    use HasOperator;

    /**
     * @desc 组合where
     * @param Query $query
     * @return string
     */
    private function compileWhereString(Query $query)
    {
        if (!$query->where) return '';

        $sql = [];

        foreach ($query->where as $k => $condition) {

            $sql[] = ($k != 0 ? $condition['type'] . " " : "") . $this->whereBasic($condition);
        }

        return "WHERE " . join(' ', $sql);
    }

    /**
     * @desc 编译Order
     * @param Query $query
     * @return string
     */
    private function compileOrderString(Query $query)
    {
        if (!$query->order) {
            return '';
        }

        $sqls = [];

        foreach ($query->order as $order) {
            foreach ($order as $name => $sort) {
                $sqls[] = "`{$name}`" . ' ' . $sort;
            }
        }

        return 'ORDER BY ' . join(',', $sqls);
    }

    /**
     * @desc 编译limit
     * @param Query $query
     * @return string
     */
    private function compileLimitString(Query $query)
    {
        $sqls = [];

        if ($query->limit) {
            $sqls[] = "LIMIT " . $query->limit;
        }

        if (!is_null($query->offset)) {
            $sqls[] = 'OFFSET ' . $query->offset;
        }

        return join(' ', $sqls);
    }

    /**
     * @desc 更新字段组合
     * @param Query $query
     * @return string
     */
    private function compileUpdateCoulmnString(Query $query)
    {
        if (!$query->data) return '';

        return 'set ' . join(',', array_map(function ($name) {
                return $this->formatField($name) . "=?";
            }, array_keys($query->data)));
    }

    /**
     * @desc 插入字段组合
     * @param Query $query
     * @return string
     */
    private function compileInsertCoulmnString(Query $query)
    {
        if (!$query->data) return '';

        //针对批量插入
        if ($query::isBatchDataInsert($query->data)) {

            $columns = array_keys($query->data[0]);

            $placeholders = $this->batchInsertPalceholder($query->data);
        } else {
            $columns = array_keys($query->data);

            $placeholders = $this->batchInsertPalceholder([$query->data]);
        }

        return "(`" . join("`,`", $columns) . "`) VALUES " . join(",", $placeholders);
    }

    /**
     * @desc 批量插入占位符
     * @param $list
     * @return array
     */
    private function batchInsertPalceholder($list)
    {
        $placeholders = [];


        foreach ($list as $data) {

            $values = array_fill(0, count($data), "?");

            $placeholders[] = "(" . join(",", $values) . ")";
        }

        return $placeholders;
    }

    private function whereBasic($where)
    {
        $method = "where" . $this->operatorMappingFormat($where['operator']);

        if (method_exists($this, $method)) {
            return $this->$method($where['name'], $where['value']);
        }

        return $this->formatField($where['name']) . ' ' . $this->operatorInterpret($where['operator']) . ' ' . '?';
    }

    private function formatField($name)
    {
        if (strpos($name, '.') !== false) {
            return '`' . join('`.`', explode('.', $name)) . '`';
        }
        return "`{$name}`";
    }

    private function whereLike($name, $value)
    {
        return $this->formatField($name) . " like ?";
    }

    private function whereIn($name, $value)
    {
        return $this->formatField($name) . " in (" . join(',', array_fill(0, count($value), '?')) . ")";
    }

    /**
     * @desc 查询字段
     * @param Query $query
     * @return string
     */
    public function compileColumnsString(Query $query)
    {
        if (!$query->columns) {
            return '*';
        }

        return join(',', array_map(function ($name) {
            return $this->formatField($name);
        }, $query->columns));
    }

    /**
     * @desc 查询sql
     * @param Query $query
     * @return string
     */
    public function compileSqlSelect(Query $query)
    {
        $sql   = "SELECT {$this->compileColumnsString($query)} FROM " . $query->table() . $this->wrapTable($query);
        $where = $this->compileWhereString($query);
        $join  = $this->compileJoins($query);
        $limit = $this->compileLimitString($query);
        $order = $this->compileOrderString($query);

        return "{$sql} {$join} {$where} {$order} {$limit}";
    }

    /**
     * @desc 清除绑定参数
     * @param Query $query
     * @param $types
     */
    public function clearBindings(Query $query, $types)
    {
        $types = (array)$types;

        foreach ($types as $type) {

            if ($type == 'where') {
                $query->where = [];
            }

            if ($type == 'order') {
                $query->order = [];
            }

            $query->binding[$type] = [];
        }
    }

    /**
     * 组合预绑定参数
     * @param Query $query
     * @param $types
     * @param bool $clearBindings
     * @return array
     */
    public function compileBinding(Query $query, $types, $clearBindings = true)
    {
        $types  = (array)$types;
        $index  = 1;
        $result = [];

        foreach ($types as $type) {
            foreach ($query->binding[$type] as $bind) {
                $result[$index++] = $bind;
            }
        }

        $clearBindings && $this->clearBindings($query, $types);

        return $result;
    }

    /**
     * @desc 查询预绑定参数
     * @param Query $query
     * @param bool $clearBindings
     * @return array
     */
    public function compileBindingForSelect(Query $query, $clearBindings = true)
    {
        return $this->compileBinding($query, ['where'], $clearBindings);
    }

    /**
     * @desc 更新sql
     * @param Query $query
     * @return string
     */
    public function compileSqlUpdate(Query $query)
    {
        $sql     = "update " . $query->table();
        $where   = $this->compileWhereString($query);
        $colmuns = $this->compileUpdateCoulmnString($query);

        return "{$sql} {$colmuns} {$where}";
    }

    /**
     * @desc 更新预绑定参数
     * @param Query $query
     * @return array
     */
    public function compileBindingForUpdate(Query $query)
    {
        return $this->compileBinding($query, ['update', 'where']);
    }

    public function compileBindingForInsert(Query $query)
    {
        return $this->compileBindingForUpdate($query);
    }

    /**
     * @desc 删除sql
     * @param Query $query
     * @return string
     */
    public function compileSqlDelete(Query $query)
    {
        $sql   = "DELETE FROM " . $query->table();
        $where = $this->compileWhereString($query);

        return "{$sql} {$where}";
    }

    /**
     * @desc 更新预绑定参数
     * @param Query $query
     * @return array
     */
    public function compileBindingForDelete(Query $query)
    {
        return $this->compileBinding($query, ['where']);
    }

    /**
     * @desc 插入sql
     * @param Query $query
     * @return string
     */
    public function compileInsertSql(Query $query)
    {
        $sql = "INSERT INTO " . $query->table();
        $sql .= $this->compileInsertCoulmnString($query);

        return $sql;
    }

    /**
     * @desc 包装表名
     * @param Query $query
     * @return string
     */
    private function wrapTable(Query $query)
    {
        return $query->alias ? ' as ' . $query->alias : '';
    }

    /**
     * @param Query $query
     * @param $fun
     * @param $column
     * @return string
     */
    public function compileAggregate(Query $query, $fun, $column)
    {
        $sql   = "SELECT {$fun}({$column}) FROM " . $query->table() . $this->wrapTable($query);
        $where = $this->compileWhereString($query);
        $join  = $this->compileJoins($query);

        return "{$sql} {$join} {$where}";
    }

    /**
     * @param Query $query
     * @return string
     */
    protected function compileJoins(Query $query)
    {
        return collect($query->join)->map(function ($join) use ($query) {
            $table   = $join['table'];
            $type    = $join['type'];
            $foregin = $join['foregin'];
            $local   = $join['local'];

            return trim("{$type} join {$table} on {$local}={$foregin}");
        })->implode(' ');
    }

}