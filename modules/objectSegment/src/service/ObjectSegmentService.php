<?php

namespace modules\objectSegment\src\service;

use modules\objectSegment\components\ObjectSegmentBaseModel;

class ObjectSegmentService
{
    /**
     * @param array $rules
     * @param string|null $prefix
     * @return string
     */
    public static function conditionDecode(array $rules = [], ?string $prefix = 'r.sub.'): string
    {
        $strArr = [];
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


                    if (in_array($rule['operator'], [ObjectSegmentBaseModel::OP_IN, ObjectSegmentBaseModel::OP_NOT_IN], true)) {
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


                    if (($rule['operator'] === ObjectSegmentBaseModel::OP_EQUAL2 || $rule['operator'] === '===') && is_array($value)) {
                        $rule['operator'] = ObjectSegmentBaseModel::OP_IN;
                        $values           = $value;
                    }
                    $field = $rule['field'] ?? $rule['id'];

                    if ($prefix) {
                        $field = $prefix . $field;
                    }

                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }

                    switch ($rule['operator']) {
                        case ObjectSegmentBaseModel::OP_EQUAL:
                        case ObjectSegmentBaseModel::OP_EQUAL2:
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_NOT_EQUAL:
                        case ObjectSegmentBaseModel::OP_NOT_EQUAL2:
                            $operator = $field . ' != ' . $value;
                            break;
                        case '!==':
                            $operator = $field . ' !== ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_LESS:
                        case '<':
                            $operator = $field . ' < ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_LESS_OR_EQUAL:
                        case '<=':
                            $operator = $field . ' <= ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_GREATER:
                        case '>':
                            $operator = $field . ' > ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_GREATER_OR_EQUAL:
                        case '>=':
                            $operator = $field . ' >= ' . $value;
                            break;
                        case ObjectSegmentBaseModel::OP_IS_EMPTY:
                            $operator = $field . ' == ""';
                            break;
                        case ObjectSegmentBaseModel::OP_IS_NOT_EMPTY:
                            $operator = $field . ' != ""';
                            break;
                        case ObjectSegmentBaseModel::OP_IS_NULL:
                            $operator = $field . ' == null';
                            break;
                        case ObjectSegmentBaseModel::OP_IS_NOT_NULL:
                            $operator = $field . ' != null';
                            break;
                        case ObjectSegmentBaseModel::OP_BETWEEN:
                            $operator = $field . ' >= ' . $value[0] . ' && ' . $field . ' <= ' . $value[1];
                            break;
                        case ObjectSegmentBaseModel::OP_NOT_BETWEEN:
                            $operator = $field . ' < ' . $value[0] . ' || ' . $field . ' > ' . $value[1];
                            break;
                        case ObjectSegmentBaseModel::OP_MATCH:
                            $operator = $field . ' matches ' . $value . '';
                            break;
                        case ObjectSegmentBaseModel::OP_IN:
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
                        case ObjectSegmentBaseModel::OP_IN_ARRAY:
                            $operator = $value . ' in ' . $field;
                            break;
                        case ObjectSegmentBaseModel::OP_NOT_IN:
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
                        case ObjectSegmentBaseModel::OP_NOT_IN_ARRAY:
                            $operator = $value . ' not in ' . $field;
                            break;
                        case ObjectSegmentBaseModel::OP_CONTAINS:
                            $operator = '';
                            if (is_array($value)) {
                                $opList = [];
                                foreach ($value as $val) {
                                    $opList[] = '"' . $val . '" in ' . $field;
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

                $strItem  .= ')';
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
