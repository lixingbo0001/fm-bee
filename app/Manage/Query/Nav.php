<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/20
 * Time: 下午7:41
 */

namespace App\Manage\Query;


use Core\Database\Query\Builder;


/**
 * @method \App\Manage\Entity\Nav first()
 * @method \App\Manage\Entity\Nav find($id)
 */
class Nav extends Builder
{

    static $table = 'nav';

    function getEntity()
    {
        return new \App\Manage\Entity\Nav();
    }

    static function call()
    {
        return new self();
    }

    public function relationPage()
    {
        return $this
            ->alias('n')
            ->select('n.id', 'r.nav_name', 'r.node_name')
            ->leftJoin(Relation::$table . ' as r', 'n.id', 'nav')
            ->dynamicWhereLike('nav_name', 'node_name')
            ->paginate();
    }

    public function getByKey($key)
    {
        return $this->where('key', $key)->first();
    }

    public function findByPid($pid)
    {
        return $this->where('pid', $pid)->first();
    }

}