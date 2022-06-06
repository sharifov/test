<?php

namespace modules\abac\src;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacPolicy;
use src\helpers\app\AppHelper;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class AbacService
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



                    if (in_array($rule['operator'], [AbacBaseModel::OP_IN, AbacBaseModel::OP_NOT_IN], true)) {
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




                    if (($rule['operator'] === AbacBaseModel::OP_EQUAL2 || $rule['operator'] === '===') && is_array($value)) {
                        $rule['operator'] = AbacBaseModel::OP_IN;
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
                        case AbacBaseModel::OP_EQUAL:
                        case AbacBaseModel::OP_EQUAL2:
                            $operator = $field . ' == ' . $value;
                            break;
                        case '===':
                            $operator = $field . ' === ' . $value;
                            break;
                        case AbacBaseModel::OP_NOT_EQUAL:
                        case AbacBaseModel::OP_NOT_EQUAL2:
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
                            $operator = $field . ' in [' . implode(',', $valArr) . ']';
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
                            $operator = $field . ' not in [' . implode(',', $valArr) . ']';
                            break;
                        case AbacBaseModel::OP_NOT_IN_ARRAY:
//                            if ($rule['type'] === 'string') {
//                                $value = '"' . $value . '"';
//                            }
                            $operator = $value . ' not in ' . $field;
                            break;
                        case AbacBaseModel::OP_CONTAINS:
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
     * @param string $dump
     * @return AbacPolicy|null
     * @throws \yii\db\Exception
     */
    public static function convertDumpToObject(string $dump): ?AbacPolicy
    {
        $object = null;
        $errors = [];
        $data = Json::decode(base64_decode($dump), false);

        if ($data) {
            if (empty($data->ap_subject)) {
                $errors[] = 'Empty subject field';
            }
            if (empty($data->ap_subject_json)) {
                $errors[] = 'Empty subject_json field';
            }

            if (empty($data->ap_object)) {
                $errors[] = 'Empty ap_object field';
            }
            if (empty($data->ap_action)) {
                $errors[] = 'Empty action field';
            }

            if (empty($data->ap_action_json)) {
                $errors[] = 'Empty action_json field';
            }

            if (!isset($data->ap_effect)) {
                $errors[] = 'Not isset effect field';
            }

            if (empty($errors)) {
                $policy = new AbacPolicy();
                $policy->ap_subject = $data->ap_subject;
                $policy->ap_subject_json = $data->ap_subject_json;
                $policy->ap_object = $data->ap_object;
                $policy->ap_action = $data->ap_action;
                $policy->ap_action_json = $data->ap_action_json;
                $policy->ap_effect = $data->ap_effect;
                $policy->ap_enabled = isset($data->ap_enabled) ? (bool) $data->ap_enabled : true;
                $policy->ap_sort_order = $data->ap_sort_order ?? 50;
                $policy->ap_rule_type = $data->ap_rule_type ?? 'p';
                $policy->ap_title = $data->ap_title ?? 'Import from dump';

                $object = $policy;
                unset($policy);
            } else {
                throw new \yii\db\Exception(implode(', ', $errors));
            }
        }
        return $object;
    }

    /**
     * @param string $dump
     * @param bool|null $enabled
     * @return bool
     */
    public static function importPolicyFromDump(string $dump, ?bool $enabled = null): bool
    {
        try {
            $policyModel = self::convertDumpToObject($dump);
            if (!empty($policyModel)) {
                if ($enabled !== null) {
                    $policyModel->ap_enabled = $enabled;
                }

                if (!$policyModel->save()) {
                    $data['error'] = $policyModel->errors;
                    $data['attributes'] = $policyModel->attributes;
                    \Yii::error($data, 'AbacService:importPolicyFromDump:save');
                } else {
                    return true;
                }
            }
        } catch (\Throwable $throwable) {
            $data = AppHelper::throwableLog($throwable);
            $data['dump'] = $dump;
            \Yii::error($data, 'AbacService:importPolicyFromDump:Throwable');
        }
        return false;
    }


    /**
     * @param string $dump
     * @return bool
     */
    public static function removePolicyFromDump(string $dump): bool
    {

        try {
            $policyModel = self::convertDumpToObject($dump);
            if (!empty($policyModel)) {
                $policy = self::findByHash($policyModel->generateHashCode());
                if ($policy) {
                    $policy->delete();
                    return true;
                }
            }
        } catch (\Throwable $throwable) {
            $data = AppHelper::throwableLog($throwable);
            $data['dump'] = $dump;
            \Yii::error($data, 'AbacService:importPolicyFromDump:Throwable');
        }

        return false;
    }

    /**
     * @param string $hashCode
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findByHash(string $hashCode)
    {
        return AbacPolicy::find()->where(['ap_hash_code' => $hashCode])
            ->orderBy(['ap_id' => SORT_DESC])->limit(1)->one();
    }
}
