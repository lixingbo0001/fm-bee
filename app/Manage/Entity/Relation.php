<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/28
 * Time: 下午2:14
 */

namespace App\Manage\Entity;


use Core\Database\Entry\BaseEntity;

class Relation extends BaseEntity
{
    public $id;
    public $nav_id;
    public $node_id;
    public $nav_name;
    public $node_name;
}