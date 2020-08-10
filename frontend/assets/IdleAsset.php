<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class IdleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'/js/timer.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/jquery.idle@1.3.0/jquery.idle.min.js',
//        '/js/idle.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
