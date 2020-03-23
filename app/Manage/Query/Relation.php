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
 * @method \App\Manage\Entity\Relation first()
 * @method \App\Manage\Entity\Relation find($id)
 */
class Relation extends Builder
{

    static $table = 'relation';

    function getEntity()
    {
        return new \App\Manage\Entity\Relation();
    }

    static function call()
    {
        return new self();
    }

    public function pageWithNav($id)
    {
        return $this
            ->alias('r')
            ->where('r.nav_id', $id)
            ->select('r.id', 'node.name', 'node.path', 'node.uri', 'node.nav', 'node.method', 'node.is_ignore', 'node.group_id', 'node.module')
            ->join(Node::$table . ' as node', 'r.node_id', 'node.id')
            ->get();
    }

    public function pageWithNode($id)
    {
        return $this
            ->alias('r')
            ->where('r.node_id', $id)
            ->select('r.id', 'nav.name', 'nav.key', 'nav.module')
            ->join(Nav::$table . ' as nav', 'r.nav_id', 'nav.id')
            ->get();
    }


}