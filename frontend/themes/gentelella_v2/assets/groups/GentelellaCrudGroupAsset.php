<?php


namespace frontend\themes\gentelella_v2\assets\groups;

class GentelellaCrudGroupAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/';
    public $baseUrl = '@web';

    public $css = [
        'css/custom.min.css',
        'css/style-crud.css'
    ];

    public $js = [
        'js/custom.js',

        'js/common.js',

        'js/util.js',
        'js/extension.js',
    ];
}
