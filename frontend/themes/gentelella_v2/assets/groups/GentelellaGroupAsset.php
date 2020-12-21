<?php
namespace frontend\themes\gentelella_v2\assets\groups;

class GentelellaGroupAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/src/';
    public $baseUrl = '@web';

    public $css = [
        'css/custom.min.css',

        'css/style-req.css'
    ];

    public $js = [
        'js/custom.js',

        'js/common.js',

        'js/util.js',
        'js/extension.js',
    ];
}