<?php

namespace common\components\bootstrap4\activeForm\assets;

use yii\web\AssetBundle;

class ActiveFormAsset extends AssetBundle
{
    public $sourcePath = '@common/components/bootstrap4/activeForm/assets';
    public $js = [
        'yii.activeForm.js',
    ];
    public $depends = [
        \yii\widgets\ActiveFormAsset::class,
    ];
}
