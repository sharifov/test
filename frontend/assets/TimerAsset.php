<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TimerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'/js/timer.css',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.9.0/timer.jquery.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
