<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/29
 * Time: 下午1:23
 */

namespace Core\Database\Query\Test;


use Core\Database\Entry\BaseEntity;
use Core\Database\Query\Builder;
use PHPUnit\Framework\TestCase;


class Nav extends BaseEntity
{
    public $name;
    public $key;
}

class UserQuery extends Builder
{

    public static $table = "nav";

    public function getEntity()
    {
        return new Nav();
    }


}

class GrammerTest extends TestCase
{

    public function test_insert()
    {
        $query = new UserQuery();

        $id = $query->insert([
            [
                'id'   => 1,
                'name' => 'blue'
            ],
            [
                'id'   => 2,
                'name' => 'blue'
            ]
        ]);

        $this->assertEquals(2, $id);
    }

    public function test_pagination()
    {
        $query = new UserQuery();

        $page = $query->paginate(1);

        $this->assertGreaterThanOrEqual(1, $page->getSize());
    }

    public function test_count()
    {
        $query = new UserQuery();

        $this->assertGreaterThanOrEqual(1, $query->count());
    }

    public function test_get()
    {
        $query = new UserQuery();

        $row = $query->find(1);

        dump($row->toArray());

        $this->assertEquals(1, $row->id);
    }

    public function test_update()
    {
        $query = new UserQuery();

        $row = $query->find(1);

        $row->update([
            'name' => mt_rand(0, 10000)
        ]);

        $this->assertEquals(1, $row->id);
    }

    public function test_delete()
    {
        $query = new UserQuery();

        $row = $query->find(1);

        $count = $row->delete();

        $query->delete(2);

        $this->assertEquals(1, $count);
    }
}