<?php

namespace frontend\assets;

use frontend\assets\groups\BootstrapGroupAsset;
use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DrawerJsAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        //'/js/drawerjs/1/drawerJs.css',
        'https://uicdn.toast.com/tui-image-editor/latest/tui-image-editor.css'
    ];
    public $js = [
//        '/js/drawerjs/1/drawerJs.standalone.js'
        'https://uicdn.toast.com/tui-image-editor/latest/tui-image-editor.js'
    ];



//    public $depends = [
//        JqueryAsset::class,
//        BootstrapGroupAsset::class
//    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

}
