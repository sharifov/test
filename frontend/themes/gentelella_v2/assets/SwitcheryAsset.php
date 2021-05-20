<?php


namespace frontend\themes\gentelella_v2\assets;

use yii\web\AssetBundle;

class SwitcheryAsset extends AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/src/';
    public $baseUrl = '@web';

    public $css = [
        'js/switchery/switchery.min.css'
    ];

    public $js = [
        'js/switchery/switchery.min.js'
    ];
}
