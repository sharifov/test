<?php

namespace modules\taskList\src\services;

use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use modules\taskList\src\objects\BaseTaskObject;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;

class TaskListService
{
    public static function getObjectAttributeList(string $object)
    {
        //$object
        //$list = Yii::$app->abac->getAttributeListByObject($this->ap_object);
        return $list;
    }

    /**
     * @param string|null $object
     * @return array
     * @throws InvalidConfigException
     */
    final public function getAttributeListByObject(?string $object = null): array
    {

        $object = TaskObject::getByName($object);

        $defaultList = $this->getDefaultAttributeList();
        $objList = [];
        if ($object) {
            $list = $this->getObjectAttributeList();
            if (isset($list[$object]) && is_array($list[$object])) {
                $objList = $list[$object];
            }
        }

        return array_merge($objList, $defaultList);
    }

    /**
     * @param string|null $objectName
     * @return array
     * @throws InvalidConfigException
     */
    public static function getOptionListByObject(?string $objectName = null): array
    {
        $list = [];
        if (!empty($objectName)) {
            $object = TaskObject::getByName($objectName);
            if ($object) {
                $list = $object::getObjectOptionList();
            }
        }
        return $list;
    }


    /**
     * @param string|null $objectName
     * @return array
     * @throws InvalidConfigException
     */
    public static function getTargetObjectListByObject(?string $objectName = null): array
    {
        $list = [];
        if (!empty($objectName)) {
            $object = TaskObject::getByName($objectName);
            if ($object) {
                $list = $object::getTargetObjectList();
            }
        }
        return $list;
    }



    /**
     * @param string|null $objectName
     * @return array
     * @throws InvalidConfigException
     */
    public static function getDefaultOptionDataByObject(?string $objectName = null): array
    {
        $data = [];
        $optionList = self::getOptionListByObject($objectName);
        if (!empty($optionList)) {
            foreach ($optionList as $name => $item) {
                $data[$name] = $item['value'] ?? null;
            }
        }
        return $data;
    }





    /**
     * @param array $rules
     * @param string|null $prefix
     * @return string
     */
    public static function conditionDecode(array $rules = [], ?string $prefix = ''): string
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



                    if (in_array($rule['operator'], [BaseTaskObject::OP_IN, BaseTaskObject::OP_NOT_IN], true)) {
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




                    if (
                        ($rule['operator'] === BaseTaskObject::OP_EQUAL2 || $rule['operator'] === '===')
                        && is_array($value)
                    ) {
                        $rule['operator'] = BaseTaskObject::OP_IN;
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
                        case BaseTaskObject::OP_EQUAL:
                        case BaseTaskObject::OP_EQUAL2:
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case BaseTaskObject::OP_NOT_EQUAL:
                        case BaseTaskObject::OP_NOT_EQUAL2:
                            $operator = $field . ' != ' . $value;
                            break;
                        case '!==':
                            $operator = $field . ' !== ' . $value;
                            break;
                        case BaseTaskObject::OP_LESS:
                        case '<':
                            $operator = $field . ' < ' . $value;
                            break;
                        case BaseTaskObject::OP_LESS_OR_EQUAL:
                        case '<=':
                            $operator = $field . ' <= ' . $value;
                            break;
                        case BaseTaskObject::OP_GREATER:
                        case '>':
                            $operator = $field . ' > ' . $value;
                            break;
                        case BaseTaskObject::OP_GREATER_OR_EQUAL:
                        case '>=':
                            $operator = $field . ' >= ' . $value;
                            break;
                        case BaseTaskObject::OP_IS_EMPTY:
                            $operator = $field . ' == ""';
                            break;
                        case BaseTaskObject::OP_IS_NOT_EMPTY:
                            $operator = $field . ' != ""';
                            break;
                        case BaseTaskObject::OP_IS_NULL:
                            $operator = $field . ' == null';
                            break;
                        case BaseTaskObject::OP_IS_NOT_NULL:
                            $operator = $field . ' != null';
                            break;
                        case BaseTaskObject::OP_BETWEEN:
                            $operator = $field . ' >= ' . $value[0] . ' && ' . $field . ' <= ' . $value[1];
                            break;
                        case BaseTaskObject::OP_NOT_BETWEEN:
                            $operator = $field . ' < ' . $value[0] . ' || ' . $field . ' > ' . $value[1];
                            break;
                        case BaseTaskObject::OP_MATCH:
                            $operator = $field . ' matches ' . $value . '';
                            break;
                        case BaseTaskObject::OP_IN:
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
                        case BaseTaskObject::OP_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' in ' . $field;
                            break;
                        case BaseTaskObject::OP_NOT_IN:
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
                        case BaseTaskObject::OP_NOT_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' not in ' . $field;
                            break;
                        case BaseTaskObject::OP_CONTAINS:
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
}
