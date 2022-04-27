<?php

namespace frontend\widgets\cronExpression;

use frontend\assets\VueAsset;
use yii\web\YiiAsset;

class CronExpressionAssets extends \yii\web\AssetBundle
{
    public $sourcePath = ('@frontend/widgets/cronExpression/assets/');

    public $js = [
        'js/day-input-component.js',
        'js/month-input-component.js',
        'js/weekday-input-component.js',
        //'js/year-input-component.js',
        'js/cron-expression.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public $depends = [
        YiiAsset::class,
        VueAsset::class
    ];

    public $publishOptions = [
        'forceCopy' => false,
    ];
}
