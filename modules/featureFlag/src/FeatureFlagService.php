<?php

namespace modules\featureFlag\src;

use modules\featureFlag\components\FeatureFlagBaseModel;
use modules\featureFlag\src\entities\FeatureFlag;
use yii\base\Exception;
use yii\helpers\VarDumper;

class FeatureFlagService
{
    /**
     * @param array $rules
     * @param string|null $prefix
     * @return string
     */
    public static function conditionDecode(array $rules = [], ?string $prefix = 'r.sub.'): string
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



                    if (in_array($rule['operator'], [FeatureFlagBaseModel::OP_IN, FeatureFlagBaseModel::OP_NOT_IN], true)) {
                        if (is_string($value)) {
                            $values = explode(',', $value);
                        } else {
                            $values = $value;
                        }
                    } else {
                        $values = [];
                    }

                    if ($rule['type'] === 'string' || $rule['type'] === 'date' || $rule['type'] === 'time') {
                        if (!is_array($value)) {
                            $value = '"' . $value . '"';
                        }
                    }




                    if (($rule['operator'] === FeatureFlagBaseModel::OP_EQUAL2 || $rule['operator'] === '===') && is_array($value)) {
                        $rule['operator'] = FeatureFlagBaseModel::OP_IN;
                        $values = $value;
                    }


                    //$field = '$var[\'' . $rule['id'] . '\']';
                    $field = $rule['field'] ?? $rule['id'];

                    if ($prefix) {
                        $field = $prefix . $field;
                    }

                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }

                    switch ($rule['operator']) {
                        case FeatureFlagBaseModel::OP_EQUAL:
                        case FeatureFlagBaseModel::OP_EQUAL2:
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_NOT_EQUAL:
                        case FeatureFlagBaseModel::OP_NOT_EQUAL2:
                            $operator = $field . ' != ' . $value;
                            break;
                        case '!==':
                            $operator = $field . ' !== ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_LESS:
                        case '<':
                            $operator = $field . ' < ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_LESS_OR_EQUAL:
                        case '<=':
                            $operator = $field . ' <= ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_GREATER:
                        case '>':
                            $operator = $field . ' > ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_GREATER_OR_EQUAL:
                        case '>=':
                            $operator = $field . ' >= ' . $value;
                            break;
                        case FeatureFlagBaseModel::OP_IS_EMPTY:
                            $operator = $field . ' == ""';
                            break;
                        case FeatureFlagBaseModel::OP_IS_NOT_EMPTY:
                            $operator = $field . ' != ""';
                            break;
                        case FeatureFlagBaseModel::OP_IS_NULL:
                            $operator = $field . ' == null';
                            break;
                        case FeatureFlagBaseModel::OP_IS_NOT_NULL:
                            $operator = $field . ' != null';
                            break;
                        case FeatureFlagBaseModel::OP_BETWEEN:
                            $operator = $field . ' >= ' . $value[0] . ' && ' . $field . ' <= ' . $value[1];
                            break;
                        case FeatureFlagBaseModel::OP_NOT_BETWEEN:
                            $operator = $field . ' < ' . $value[0] . ' || ' . $field . ' > ' . $value[1];
                            break;
                        case FeatureFlagBaseModel::OP_MATCH:
                            $operator = $field . ' matches ' . $value . '';
                            break;
                        case FeatureFlagBaseModel::OP_IN:
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
                            $operator = $field . ' in [' . implode(',', $valArr) . ']';
                            break;
                        case FeatureFlagBaseModel::OP_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' in ' . $field;
                            break;
                        case FeatureFlagBaseModel::OP_NOT_IN:
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
                            $operator = $field . ' not in [' . implode(',', $valArr) . ']';
                            break;
                        case FeatureFlagBaseModel::OP_NOT_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' not in ' . $field;
                            break;
                        case FeatureFlagBaseModel::OP_CONTAINS:
                            $operator = '';
                            if (is_array($value)) {
                                $opList = [];
                                foreach ($value as $val) {
                                    $opList[] =  '"' . $val . '" in ' . $field;
                                }
                                if ($opList) {
                                    $operator .= implode(' || ', $opList);
                                }
                            }
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

    /**
     * @param string $key
     * @param string $name
     * @param string $type
     * @param string $value
     * @param int|null $enableType
     * @param array|null $fields
     * @return void
     * @throws Exception
     */
    public static function add(
        string $key,
        string $name,
        string $type,
        string $value,
        ?int $enableType = FeatureFlag::ET_ENABLED,
        ?array $fields = []
    ) {
        $ff = new FeatureFlag();

        if ($fields) {
            $ff->attributes = $fields;
        }

        $ff->ff_key = $key;
        $ff->ff_name = $name;
        $ff->ff_type = $type;
        $ff->ff_value = $value;
        $ff->ff_enable_type = $enableType;
        if (!$ff->save()) {
            throw new Exception($ff->errors);
        }
    }

    public static function delete(
        string $key
    ): int {
        return FeatureFlag::deleteAll(['key' => $key]);
    }
}
