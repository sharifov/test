<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class EditToolAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/edit-tool.js'
    ];
    public $depends = [
        \yii\web\YiiAsset::class
    ];
}
