<?php

namespace src\access;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ConditionExpressionService implements ConditionExpressionInterface
{
    public static function isValidCondition(string $condition, array $data): bool
    {
        return (bool) (new ExpressionLanguage())->evaluate($condition, $data);
    }

    public static function decode(array $rules = [], ?string $prefix = ''): string
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
                    $strItem .= self::decode($rule);
                } else {
                    $value = $rule['value'];

                    if (in_array($rule['operator'], [self::OP_IN, self::OP_NOT_IN], true)) {
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
                        ($rule['operator'] === self::OP_EQUAL2 || $rule['operator'] === '===')
                        && is_array($value)
                    ) {
                        $rule['operator'] = self::OP_IN;
                        $values = $value;
                    }

                    $field = $rule['field'] ?? $rule['id'];

                    if ($prefix) {
                        $field = $prefix . $field;
                    }

                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }

                    switch ($rule['operator']) {
                        case self::OP_EQUAL:
                        case self::OP_EQUAL2:
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case self::OP_NOT_EQUAL:
                        case self::OP_NOT_EQUAL2:
                            $operator = $field . ' != ' . $value;
                            break;
                        case '!==':
                            $operator = $field . ' !== ' . $value;
                            break;
                        case self::OP_LESS:
                        case '<':
                            $operator = $field . ' < ' . $value;
                            break;
                        case self::OP_LESS_OR_EQUAL:
                        case '<=':
                            $operator = $field . ' <= ' . $value;
                            break;
                        case self::OP_GREATER:
                        case '>':
                            $operator = $field . ' > ' . $value;
                            break;
                        case self::OP_GREATER_OR_EQUAL:
                        case '>=':
                            $operator = $field . ' >= ' . $value;
                            break;
                        case self::OP_IS_EMPTY:
                            $operator = $field . ' == ""';
                            break;
                        case self::OP_IS_NOT_EMPTY:
                            $operator = $field . ' != ""';
                            break;
                        case self::OP_IS_NULL:
                            $operator = $field . ' == null';
                            break;
                        case self::OP_IS_NOT_NULL:
                            $operator = $field . ' != null';
                            break;
                        case self::OP_BETWEEN:
                            $operator = $field . ' >= ' . $value[0] . ' && ' . $field . ' <= ' . $value[1];
                            break;
                        case self::OP_NOT_BETWEEN:
                            $operator = $field . ' < ' . $value[0] . ' || ' . $field . ' > ' . $value[1];
                            break;
                        case self::OP_MATCH:
                            $operator = $field . ' matches ' . $value . '';
                            break;
                        case self::OP_IN:
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
                        case self::OP_IN_ARRAY:
                            $operator = $value . ' in ' . $field;
                            break;
                        case self::OP_NOT_IN:
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
                        case self::OP_NOT_IN_ARRAY:
                            $operator = $value . ' not in ' . $field;
                            break;
                        case self::OP_CONTAINS:
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

        return implode(' ' . $and_or_value . ' ', $strArr);
    }
}
