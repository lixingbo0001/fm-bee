<?php

namespace Core\Controller\Traits;


use App\Exceptions\ValidateException;

trait ValidateTrait
{

    final public function validate()
    {
        $rule_map = array_get((array)$this->rules(), $this->getAction());

        if (!$rule_map) return;

        list($rules, $messages) = $rule_map;

        $validator = \validator(request()->request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new  ValidateException($validator);
        }
    }

    protected function rules():array
    {
        return [];
    }
}
