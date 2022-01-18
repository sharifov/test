<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class Html2CanvasAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        //'',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.0/html2canvas.min.js'
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
