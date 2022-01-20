<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BrowserReportAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
//        'https://cdn.jsdelivr.net/npm/browser-report@2.2.12/index.js',
        'js/browser-report.js',
    ];



//    public $depends = [
//        JqueryAsset::class,
//        BootstrapGroupAsset::class
//    ];

//    public $jsOptions = [
//        'position' => \yii\web\View::POS_END
//    ];

}