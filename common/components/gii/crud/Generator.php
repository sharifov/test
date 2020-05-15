<?php

namespace common\components\gii\crud;

use yii\helpers\ArrayHelper;

class Generator extends \yii\gii\generators\crud\Generator
{
    public $enablePjax = true;

    public function generateSearchRules(): array
    {
        $rules = [];
        foreach (parent::generateSearchRules() as $line) {
            $common = mb_stristr($line, ']', true);
            $validator = mb_stristr($line, ']');
            preg_match_all('(\w+)', $common, $matches);
            if (isset($matches[0])) {
                foreach ($matches[0] as $attr) {
                    $rules[] = [
                        'attribute' => $attr,
                        'rule' => '[\'' . $attr . '\'' . substr($validator, 1, strlen($validator))
                    ];
                }
            }
        }
        ArrayHelper::multisort($rules, 'attribute');
        $attr = null;
        $tmpRules = [];
        foreach ($rules as $rule) {
            if (($attr !== null) && $attr !== $rule['attribute']) {
                $tmpRules[] = '';
            }
            $attr = $rule['attribute'];
            $tmpRules[] = $rule['rule'];
        }
        return $tmpRules;
    }
}
