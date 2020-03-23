<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/25
 * Time: 下午2:25
 */

namespace Core\Pagination;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Paginator implements Arrayable, \JsonSerializable, Jsonable
{

    private $_items;
    private $_total;
    private $_page;
    private $_limit;
    private $_default_limit = 12;
    private $_max_limit     = 999;


    public function __construct($page, $limit, $max_limit)
    {
        $this->setPage($page);

        $this->setLimit($limit);

        $max_limit && ($this->_max_limit = $max_limit);
    }

    /**
     * 检查客户端输入的page合法性
     * @param $page
     * @return bool
     */
    protected function isValidPageNumber($page)
    {
        return $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false;
    }

    public function setTotal($total)
    {
        $this->_total = (int)$total;
    }

    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * 设置每页条数
     * @param $limit
     */
    public function setLimit($limit)
    {
        $limit = $limit >= 1 ? (int)$limit : $this->_default_limit;

        $this->_limit = min($limit, $this->_max_limit);
    }

    /**
     * 获取每页条数
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * 设置当前页
     * @param $page
     */
    public function setPage($page)
    {
        $this->_page = $this->isValidPageNumber($page) ? (int)$page : 1;
    }

    /**
     * 获取当前页
     * @return mixed
     */
    public function getPage()
    {
        return $this->_page;
    }

    public function setItems($items)
    {
        $this->_items = $items;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getSize()
    {
        return count($this->_items);
    }

    /**
     * 赋予可以转数组的能力
     * @return array
     */
    public function toArray()
    {

        return [
            'page'  => $this->getPage(),
            'total' => $this->getTotal(),
            'size'  => $this->getSize(),
            'list'  => $this->getItems()
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * 赋予可以被序列化的能力
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


}