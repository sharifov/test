<?php

namespace frontend\themes\gentelella_v2\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CommonAsset extends AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/';
    public $baseUrl = '@web';

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
