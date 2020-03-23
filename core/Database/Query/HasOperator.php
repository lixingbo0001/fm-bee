<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/21
 * Time: 下午5:35
 */

namespace Core\Database\Query;

use Core\Database\Query\Exception\SqlException;

trait HasOperator
{
    public $_operators = [
        'eq', 'neq', 'like', 'nlike', 'gt', 'lt', 'egt', 'elt', 'between', 'nbetween', 'in'
    ];

    private $_operators_mapping = [
        'eq'       => '=',
        'neq'      => '!=',
        'like'     => '~',
        'nlike'    => '!~',
        'gt'       => '>',
        'lt'       => '<',
        'egt'      => '>=',
        'elt'      => '<=',
        'between'  => '<>',
        'nbetween' => '><',
        'in'       => 'in',
    ];

    function operatorMappingFormat($op)
    {
        if (in_array($op, $this->_operators)) {
            return $op;
        }

        switch ($op) {
            case "=":
            case "":
                $op = 'eq';
                break;
            case "!":
            case "<>":
                $op = 'neq';
                break;
            case "~":
                $op = 'like';
                break;
            case "!~":
                $op = 'nlike';
                break;
            case '>':
                $op = 'gt';
                break;
            case '<':
                $op = 'lt';
                break;
            case '>=':
                $op = 'egt';
                break;
            case '<=':
                $op = 'elt';
                break;
            default:
                throw new SqlException("invalid operator [{$op}]");
        }

        return $op;
    }

    function operatorInterpret($operator)
    {
        return $this->_operators_mapping[$this->operatorMappingFormat($operator)];
    }

    function operatorInterpretJoin($join)
    {
        switch ($join) {
            case 'left':
                return '>';

            case 'right':

                return '<';
            default:
                return '><';
        }
    }
}