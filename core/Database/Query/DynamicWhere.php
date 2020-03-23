<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/28
 * Time: 下午4:04
 */

namespace Core\Database\Query;

trait DynamicWhere
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * 动态匹配-时间
     * @param $field_name
     * @param string $sname
     * @param string $ename
     * @return $this
     */
    public function dateIn($field_name, $sname = 'sdate', $ename = 'edate')
    {
        $sdate = $this->collection->get($sname) ?: null;
        $edate = $this->collection->get($ename) ?: null;

        if ($sdate && $edate) {
            return $this->where($field_name, [$sdate, $edate], 'between');
        }

        if ($sdate) {
            return $this->where($field_name, $sdate, 'egt');
        }

        if ($edate) {
            return $this->where($field_name, $edate, 'elt');
        }

        return $this;
    }

    public function dynamicWhereLike($definds)
    {
        if (!is_array($definds)) {
            $definds = func_get_args();
        }

        $wheres = [];

        foreach ($definds as $name) {
            $wheres[] = [$name, 'like', $name];
        }

        return $this->dynamicWhere($wheres);
    }

    public function dynamicWhereEq($definds)
    {
        if (!is_array($definds)) {
            $definds = func_get_args();
        }

        $wheres = [];

        foreach ($definds as $name) {
            $wheres[] = [$name, 'eq', $name];
        }

        return $this->dynamicWhere($wheres);
    }

    /**
     * 动态匹配
     * @param array $definds
     * @return $this|DynamicWhere
     */
    public function dynamicWhere(array $definds)
    {
        //必须依赖collection
        if (!$this->collection) return $this;

        $query  = $this;
        $parsed = $this->parse($definds);

        foreach ($parsed as $row) {
            list($name, $condition, $val) = $row;

            $query = $query->where($name, $val, $condition);
        }

        return $query;
    }

    /**
     * 组合where条件
     * @param $definds
     * @return array
     */
    private function parse($definds)
    {
        $result = [];

        foreach ($definds as $row) {

            $filed_key   = array_get($row, 0);
            $symbol      = array_get($row, 1);
            $request_key = array_get($row, 2);

            if (count($row) < 3) {
                $request_key = $symbol;
                $symbol      = '=';
            }

            $expressions = $this->multiplexKey($request_key, $symbol);

            foreach ($expressions as $expression) {

                list($condition, $val) = $expression;

                if ($val === null) continue;

                $result[] = [$filed_key, $condition, $val];

            }
        }

        return $result;
    }

    /**
     * 根据request预定义where
     * @param $request_key
     * @param string $condition
     * @return array
     */
    private function multiplexKey($request_key, $condition = '=')
    {
        $result = [];

        if ($this->collection->offsetExists($request_key)) {

            $result[] = $this->toExpression($condition, $request_key);
        }

        foreach ($this->grammer->_operators as $name) {

            $index = '_' . $name . '_' . $request_key;

            if ($this->collection->offsetExists($index)) {

                $result[] = $this->toExpression($name, $index);
            }
        }

        return $result;
    }

    /**
     * 转换表达式
     * @param $condition
     * @param $request_key
     * @return array
     */
    private function toExpression($condition, $request_key)
    {
        //符号条件转换成英文条件
        $condition = $this->grammer->operatorMappingFormat($condition);
        $val       = $this->collection->get($request_key);

        return [$condition, $val];
    }
}