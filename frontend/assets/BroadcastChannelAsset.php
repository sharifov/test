<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BroadcastChannelAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
//    public $css = [
//        '/js/jquery.timeline2/dist/jquery.timeline.min.css',
//
//    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/broadcast-channel@3.1.0/dist/lib/browser.min.js'
    ];
//    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap4\BootstrapPluginAsset',
//    ];
}
