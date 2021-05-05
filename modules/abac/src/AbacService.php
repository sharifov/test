<?php

namespace modules\abac\src;

use modules\abac\components\AbacBaseModel;

class AbacService
{
    /**
     * @param array $rules
     * @return string
     */
    public static function conditionDecode(array $rules = []): string
    {
        //$str = '';
        $strArr = [];

        /*public static $operators = [
        'equal' =>            '= ?',
        'not_equal' =>        '<> ?',
        'in' =>               ['op' => 'IN (?)',     'list' => true, 'sep' => ', ' ],
        'not_in' =>           ['op' => 'NOT IN (?)', 'list' => true, 'sep' => ', '],
        'less' =>             '< ?',
        'less_or_equal' =>    '<= ?',
        'greater' =>          '> ?',
        'greater_or_equal' => '>= ?',
        'between' =>          ['op' => 'BETWEEN ?',   'list' => true, 'sep' => ' AND '],
        'begins_with' =>      ['op' => 'LIKE ?',     'fn' => function($value){ return "$value%"; } ],
        'not_begins_with' =>  ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "$value%"; } ],
        'contains' =>         ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value%"; } ],
        'not_contains' =>     ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value%"; } ],
        'ends_with' =>        ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value"; } ],
        'not_ends_with' =>    ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value"; } ],
        'is_empty' =>         '= ""',
        'is_not_empty' =>     '<> ""',
        'is_null' =>          'IS NULL',
        'is_not_null' =>      'IS NOT NULL'
    ];*/
        //$rules = json_decode($this->r_rules_data)


        if (!empty($rules['condition'])) {
            switch ($rules['condition']) {
                case 'AND':
                    $and_or_value = '&&';
                    break;
                case 'OR':
                    $and_or_value = '||';
                    break;
                default:
                    $and_or_value = '&&';
            }
        } else {
            $and_or_value = '&&';
        }


        if (!empty($rules['rules'])) {
            foreach ($rules['rules'] as $rule) {
                $strItem = '(';

                if (isset($rule['rules'])) {
                    $strItem .= self::conditionDecode($rule);
                } else {
                    $value = $rule['value'];

                    if (in_array($rule['operator'], [AbacBaseModel::OP_IN, AbacBaseModel::OP_NOT_IN], true)) {
                        if (is_string($value)) {
                            $values = explode(',', $value);
                        } else {
                            $values = $value;
                        }
                    } else {
                        $values = [];
                    }

                    if ($rule['type'] === 'string') {
                        $value = '"' . $value . '"';
                    }

                    //$field = '$var[\'' . $rule['id'] . '\']';
                    $field = $rule['field'] ?? $rule['id'];
                    $field = 'r.sub.' . $field;

                    switch ($rule['operator']) {
                        case AbacBaseModel::OP_EQUAL:
                        case '==':
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case AbacBaseModel::OP_NOT_EQUAL:
                        case '!=':
                            $operator = $field . ' != ' . $value;
                            break;
                        case '!==':
                            $operator = $field . ' !== ' . $value;
                            break;
                        case AbacBaseModel::OP_LESS:
                        case '<':
                            $operator = $field . ' < ' . $value;
                            break;
                        case AbacBaseModel::OP_LESS_OR_EQUAL:
                        case '<=':
                            $operator = $field . ' <= ' . $value;
                            break;
                        case AbacBaseModel::OP_GREATER:
                        case '>':
                            $operator = $field . ' > ' . $value;
                            break;
                        case AbacBaseModel::OP_GREATER_OR_EQUAL:
                        case '>=':
                            $operator = $field . ' >= ' . $value;
                            break;
                        case AbacBaseModel::OP_IS_EMPTY:
                            $operator = $field . ' == ""';
                            break;
                        case AbacBaseModel::OP_IS_NOT_EMPTY:
                            $operator = $field . ' != ""';
                            break;
                        case AbacBaseModel::OP_IS_NULL:
                            $operator = $field . ' == null';
                            break;
                        case AbacBaseModel::OP_IS_NOT_NULL:
                            $operator = $field . ' != null';
                            break;
                        case AbacBaseModel::OP_BETWEEN:
                            $operator = $field . ' >= ' . $value[0] . ' && ' . $field . ' <= ' . $value[1];
                            break;
                        case AbacBaseModel::OP_NOT_BETWEEN:
                            $operator = $field . ' < ' . $value[0] . ' || ' . $field . ' > ' . $value[1];
                            break;
                        case AbacBaseModel::OP_MATCH:
                            $operator = $field . ' matches ' . $value . '';
                            break;
                        case AbacBaseModel::OP_IN:
                            $valArr = [];
                            if (is_array($values)) {
                                foreach ($values as $val) {
                                    $val = trim($val);
                                    if ($rule['type'] === 'string') {
                                        $val = '"' . $val . '"';
                                    }
                                    $valArr[] = $val;
                                }
                            }
                            $operator = $field . ' in (' . implode(', ', $valArr) . ')';
                            break;
                        case AbacBaseModel::OP_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' in ' . $field;
                            break;
                        case AbacBaseModel::OP_NOT_IN:
                            $valArr = [];
                            if (is_array($values)) {
                                foreach ($values as $val) {
                                    $val = trim($val);
                                    if ($rule['type'] === 'string') {
                                        $val = '"' . $val . '"';
                                    }
                                    $valArr[] = $val;
                                }
                            }
                            $operator = $field . ' not in (' . implode(', ', $valArr) . ')';
                            break;
                        case AbacBaseModel::OP_NOT_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' not in ' . $field;
                            break;
                        default:
                            $operator = $field;
                    }

                    $strItem .= $operator;
                }

                $strItem .= ')';

                $strArr[] = $strItem;
            }
        }

        $str = implode(' ' . $and_or_value . ' ', $strArr);
        return $str;
    }

    /**
     * @param string $code
     * @return string
     */
    public static function humanConditionCode(string $code = ''): string
    {
        $code = str_replace('$var[\'', ' <span class="label label-default">{', $code);
        $code = str_replace('\']', '}</span>', $code);
        $code = str_replace('&&', 'AND', $code);
        $code = str_replace('||', 'OR', $code);
        $code = str_replace('==', '=', $code);
        return $code;
    }
}
