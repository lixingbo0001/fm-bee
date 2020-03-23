<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/20
 * Time: 下午1:56
 */

namespace Core\Database\Entry;

use Core\Database\Query\Builder;
use Ddup\Part\Libs\Helper;
use Ddup\Part\Libs\Str;
use Ddup\Part\Struct\StructReadable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class BaseEntity implements Jsonable, \JsonSerializable, Arrayable
{
    /**
     * 主键
     * @var
     */
    public $id;

    protected $attrs = [];
    /**
     * @var Builder
     */
    protected $query;

    public function useData($data)
    {
        if (!$data) {
            return;
        }

        $fomated = $this->format($data);

        $this->batchSet($fomated);
    }

    protected function set($attr, $value)
    {
        if (in_array($attr, $this->propertys())) {

            $setter = "set_" . $attr;

            if (method_exists($this, $setter)) {
                $this->$attr = $this->$setter($value);
            } else {
                $this->$attr = $value;
            }
        }
    }

    public function get($name, $default = null)
    {
        $getter = 'get_' . $name;

        if (method_exists($this, $getter)) {
            return $this->$getter(array_get($this->attrs, $name, $default));
        }

        return array_get($this->attrs, $name, $default);
    }

    public function fields()
    {
        return $this->propertys();
    }

    public function table()
    {
        return Str::last(get_class($this), '\\');
    }

    public function reset()
    {
        foreach ($this->fields() as $name) {
            $this->$name = null;
        }

        $this->attrs = [];
    }

    private function format($original)
    {

        switch (gettype($original)) {
            case 'string':
                return (array)json_encode($original, true);
            case 'array':
                return $original;
        }

        if ($original instanceof StructReadable) {
            return $original->toArray();
        }

        return Helper::toArray($original);
    }

    private function batchSet($propertys)
    {
        $this->attrs = $propertys;

        foreach ($propertys as $property => $value) {
            $this->set($property, $value);
        }
    }

    public function propertys()
    {
        return array_keys($this->values());
    }

    private function values()
    {
        $attrs = get_object_vars($this);

        array_forget($attrs, ['attrs', 'query']);

        return $attrs;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function update($data)
    {
        return $this->query->update($data, $this->getId());
    }

    public function delete()
    {
        return $this->query->delete($this->getId());
    }

    public function setQuery(Builder $query)
    {
        $this->query = $query;
    }

    public function toArray()
    {
        return $this->values();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), true);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function exists()
    {
        return $this->getId() > 0;
    }

    public function same($id)
    {
        return $this->getId() == $id;
    }

}
