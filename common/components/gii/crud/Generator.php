<?php

namespace common\components\gii\crud;

use yii\helpers\ArrayHelper;

/**
 * Class Generator
 *
 * @property $baseFrontendController
 * @property $useLayoutCrud
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    public $enablePjax = true;

    public $baseFrontendController = true;

    public $useLayoutCrud = false;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['baseFrontendController', 'boolean'],
            ['useLayoutCrud', 'boolean'],
        ]);
    }
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'baseFrontendController' => 'Ignore Base Controller Class. Use FController',
            'useLayoutCrud' => 'Use Layout CRUD',
        ]);
    }

    public function hints()
    {
        return array_merge(parent::hints(), [
            'baseFrontendController' => 'Ignore Base Controller Class. Use FController',
            'useLayoutCrud' => 'Use Layout CRUD. (Disabled: Phone Widget, Client Chat Widget ...). Enable only with FController',
        ]);
    }

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
