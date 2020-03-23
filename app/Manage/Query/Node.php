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
 * @method \App\Manage\Entity\Node first()
 * @method \App\Manage\Entity\Node find($id)
 */
class Node extends Builder
{

    static $table = 'node';

    function getEntity()
    {
        return new \App\Manage\Entity\Node();
    }

    static function call()
    {
        return new self();
    }

    public function getByGroup($group_id)
    {
        return $this->where('group_id', $group_id)->first();
    }

    public function getMustGroupId($controller, $module)
    {
        $group = $this->where('group_id', 0)->where('path', $controller)->first();

        if ($group->id) {
            return $group->id;
        }

        return $this->createId([
            'path'   => $controller,
            'module' => $module
        ]);
    }

}