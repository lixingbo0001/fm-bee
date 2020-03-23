<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/28
 * Time: 下午2:14
 */

namespace App\Manage\Entity;


class Nav extends \Core\Database\Entry\BaseEntity
{
    public $name;
    public $key;
    public $sort;
    public $pid;
    public $module;
}