<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/28
 * Time: 下午2:14
 */

namespace App\Manage\Entity;


use Core\Database\Entry\BaseEntity;

class Node extends BaseEntity
{
    public $path;
    public $uri;
    public $method;
    public $name;
    public $module;
    public $group_id;
    public $is_ignore;
    public $nav;
}