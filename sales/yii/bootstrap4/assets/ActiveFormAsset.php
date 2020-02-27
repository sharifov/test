<?php

namespace sales\yii\bootstrap4\assets;

use yii\web\AssetBundle;

class ActiveFormAsset extends AssetBundle
{
    public $sourcePath = '@sales/yii/bootstrap4/assets';
    public $js = [
        'yii.activeForm.js',
    ];
    public $depends = [
        \yii\widgets\ActiveFormAsset::class,
    ];
}
