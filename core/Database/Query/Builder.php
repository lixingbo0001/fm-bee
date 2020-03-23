<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/21
 * Time: 下午5:35
 */

namespace Core\Database\Query;


use Core\Database\Entry\BaseEntity;
use Core\Database\Medoo;
use Core\Database\Query\Exception\CurdException;
use Core\Pagination\Paginator;


abstract class Builder
{

    use DynamicWhere;

    private static $_onResolver;

    protected $table;

    /**
     * @var Query
     */
    private $query;
    /**
     * @var Grammar
     */
    private $grammer;

    /**
     * @var Medoo
     */
    private $db;
    private $sql;

    protected $fillable = [];

    function __construct()
    {
        $this->db      = app('database.connecting');
        $this->query   = new Query();
        $this->grammer = new Grammar();

        $this->query->setTable($this->table);
        $this->query->select($this->fillable ?: $this->getEntity()->propertys());

        if (self::$_onResolver) (self::$_onResolver)($this);
    }

    static function registerOnResolver($callback)
    {
        self::$_onResolver = $callback;
    }

    /**
     * @return BaseEntity
     */
    abstract function getEntity();

    protected function getConnecting()
    {
        return $this->db;
    }

    public function getDb()
    {
        return $this->getConnecting();
    }

    private function withEntity($data)
    {
        $entity = $this->getEntity();

        $entity->setQuery($this);
        $entity->useData($data);

        return $entity;
    }

    public function setTable($table)
    {
        $this->query->setTable($table);

        return $this->query;
    }

    /**
     * @desc 获取表名
     * @return mixed
     */
    protected function table()
    {
        return $this->query->table();
    }

    /**
     * @desc 表别名
     * @param $alias
     * @return $this
     */
    protected function alias($alias)
    {
        $this->query->alias($alias);

        return $this;
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
        $this->query->join($table, $local, $foregin, $join);

        return $this;
    }

    public function leftJoin($table, $local, $foregin)
    {
        return $this->join($table, $local, $foregin, 'left');
    }

    /**
     * @desc 排序
     * @param $name
     * @param string $sort
     * @return $this
     */
    public function orderBy($name, $sort)
    {
        $this->query->orderBy($name, $sort);

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
    public function select(... $names)
    {
        $this->query->select($names);

        return $this;
    }

    /**
     * @desc 查询字段
     * @param $names
     * @return $this
     */
    public function addSelect($names)
    {
        $this->query->addSelect($names);

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
     * @param $name
     * @param null $value
     * @param $operator
     * @return $this
     */
    public function where($name, $value = null, $operator = 'eq')
    {
        $this->query->where(is_array($name) ? $name : [$name => $value], $operator, 'and');

        return $this;
    }

    public function whereIn($name, $value = null)
    {
        $this->query->where(is_array($name) ? $name : [$name => $value], 'in', 'and');

        return $this;
    }

    public function range($offset, $limit)
    {
        $this->query->range($offset, $limit);

        return $this;
    }

    public function limit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    public function offset($offset)
    {
        $this->query->offset($offset);

        return $this;
    }

    private function forPage($page, $limit)
    {
        return $this->range($page - 1, $limit);
    }

    public function get()
    {
        $this->sql = $this->grammer->compileSqlSelect($this->query);

        $result = $this->getDb()->fetch($this->sql, $this->grammer->compileBindingForSelect($this->query));

        return $result;
    }

    public function first()
    {
        $this->query->limit(1);

        $list = $this->get();

        return $this->withEntity($list ? $list[0] : null);
    }

    /**
     * @desc 获取一个字段
     * @param $name
     * @return mixed
     */
    public function value($name)
    {
        $this->select($name);

        return $this->first()->$name;
    }

    public function exists()
    {
        return $this->value('id') !== null;
    }

    public function find($id)
    {
        $this->where('id', $id);

        return $this->first();
    }

    public function findOrError($id, $title = '')
    {
        $entity = $this->find($id);

        if (!$entity->getId()) {
            throw new CurdException($title . "数据不存在");
        }

        return $entity;
    }

    public function insert($list)
    {
        $this->query->setData($list);

        $db = $this->getConnecting();

        $this->sql = $this->grammer->compileInsertSql($this->query);

        $db->insert($this->sql, $this->grammer->compileBindingForInsert($this->query));

        return $db->lastId();
    }

    public function createId($data)
    {
        $this->query->setData($data);

        $db = $this->getConnecting();

        $this->sql = $this->grammer->compileInsertSql($this->query);

        $db->insert($this->sql, $this->grammer->compileBindingForInsert($this->query));

        return $db->lastId();
    }

    public function createIdOrError($data)
    {
        $id = $this->createId($data);

        if (!$id) {
            throw new CurdException("创建失败");
        }

        return $id;
    }

    public function update($data = [], $id = null)
    {
        !is_null($id) && $this->where('id', $id);

        $this->query->setData($data);

        $this->sql = $this->grammer->compileSqlUpdate($this->query);

        return $this->getConnecting()->update($this->sql, $this->grammer->compileBindingForUpdate($this->query))->rowCount();
    }

    public function updateOrError($data = [], $id = null)
    {
        $rowCount = $this->update($data, $id);

        if (!$rowCount) {
            throw new CurdException("更新失败");
        }

        return $rowCount;
    }

    public function delete($id)
    {
        $this->where('id', $id);

        $this->sql = $this->grammer->compileSqlDelete($this->query);

        return $this->getConnecting()->delete($this->sql, $this->grammer->compileBindingForDelete($this->query))->rowCount();
    }

    public function deleteOrError($id)
    {
        $rowCount = $this->delete($id);

        if (!$rowCount) {
            throw new CurdException("删除失败");
        }

        return $rowCount;
    }

    public function query($sql)
    {
        return $this->getConnecting()->query($sql);
    }

    public function cloneWithout($properties)
    {
        $obj = clone $this;

        foreach ($properties as $property) {
            $obj->$property = null;
        }

        return $obj;
    }

    public function count($column = '*')
    {
        $this->sql = $this->grammer->compileAggregate($this->query, 'count', $column);

        return $this->getConnecting()->count($this->sql, $this->grammer->compileBindingForSelect($this->query));
    }

    public function getCountForPagination()
    {
        $this->sql = $this->grammer->compileAggregate($this->query, 'count', '*');

        return $this->getConnecting()->count($this->sql, $this->grammer->compileBindingForSelect($this->query, false));
    }

    /**
     * 构造分页类
     * @return Paginator
     * @throws \Core\Container\BindingResolutionException
     */
    protected function paginator()
    {
        return app()->make('paginator');
    }

    public function getSql()
    {
        return $this->sql ?: $this->grammer->compileSqlSelect($this->query);
    }

    /**
     * 分页查询
     * @param null $limit
     * @param null $page
     * @return Paginator
     * @throws \Core\Container\BindingResolutionException
     */
    public function paginate($limit = null, $page = null)
    {
        $paginator = $this->paginator();

        if ($page) $paginator->setPage($page);
        if ($limit) $paginator->setLimit($limit);

        $page  = $paginator->getPage();
        $limit = $paginator->getLimit();

        $total = $this->getCountForPagination();
        $items = $total ? $this->forPage($page, $limit)->get() : [];

        $paginator->setItems($items);
        $paginator->setTotal($total);

        return $paginator;
    }

}