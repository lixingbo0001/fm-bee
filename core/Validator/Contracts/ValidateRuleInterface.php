<?php

namespace Core\Validator\Contracts;

interface ValidateRuleInterface
{
    function rules();

    function messages();
}