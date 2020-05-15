<?php

namespace common\components\gii\model;

use yii\helpers\ArrayHelper;

class Generator extends \yii\gii\generators\model\Generator
{
    public $standardizeCapitals = true;
    public $singularize = true;

    public function generateRules($table): array
    {
        $rules = [];
        foreach (parent::generateRules($table) as $line) {
            $common = mb_stristr($line, ']', true);
            $validator = mb_stristr($line, ']');
            preg_match_all('(\w+)', $common, $matches);
            if (isset($matches[0])) {
                if (
                    count($matches[0]) === 1
                    || (strpos($validator, '], \'exist\'') !== 0 && strpos($validator, '], \'unique\'') !== 0)
                ) {
                    foreach ($matches[0] as $attr) {
                        $rules[] = [
                            'type' => 'single',
                            'attribute' => $attr,
                            'rule' => '[\'' . $attr . '\'' . substr($validator, 1, strlen($validator))
                        ];
                    }
                } else {
                    $rules[] = [
                        'type' => 'multi',
                        'attribute' => $line,
                        'rule' => $line
                    ];
                }
            }
        }
        ArrayHelper::multisort($rules, 'attribute');
        $attr = null;
        $first = true;
        $tmpRules = [];
        foreach ($rules as $rule) {
            if ($rule['type'] === 'single') {
                if ($attr !== null || !$first) {
                    if ($attr !== $rule['attribute']) {
                        $tmpRules[] = '';
                    }
                }
                $attr = $rule['attribute'];
                $tmpRules[] = $rule['rule'];
            } else {
                $attr = null;
                if (!$first) {
                    $tmpRules[] = '';
                }
                $first = false;
                $tmpRules[] = $rule['rule'];
            }
        }
        return $tmpRules;
    }
}
