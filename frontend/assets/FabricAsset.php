<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FabricAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        "https://cdn.jsdelivr.net/npm/fabric@4.6.0/dist/fabric.min.js",
    ];



//    public $depends = [
//        JqueryAsset::class,
//        BootstrapGroupAsset::class
//    ];

//    public $jsOptions = [
//        'position' => \yii\web\View::POS_END
//    ];

}