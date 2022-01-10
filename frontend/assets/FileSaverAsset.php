<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FileSaverAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js",
    ];



//    public $depends = [
//        JqueryAsset::class,
//        BootstrapGroupAsset::class
//    ];

//    public $jsOptions = [
//        'position' => \yii\web\View::POS_END
//    ];

}