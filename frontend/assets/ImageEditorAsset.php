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
        //'//uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.css',
        //'//uicdn.toast.com/tui-image-editor/v3.10.0/tui-image-editor.css'
        'https://uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.css',
        'https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.css'
    ];
    public $js = [
//        '/js/drawerjs/1/drawerJs.standalone.js'
//        'https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.js'
        "https://cdn.jsdelivr.net/npm/fabric@4.6.0/dist/fabric.min.js",
        "https://uicdn.toast.com/tui.code-snippet/latest/tui-code-snippet.js",
//        "//cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js",

        //        "https://cdn.jsdelivr.net/npm/fabric@4.6.0/dist/fabric.min.js",
//        "https://cdnjs.cloudflare.com/ajax/libs/tui-code-snippet/2.3.2/tui-code-snippet.min.js",
        "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js",


        "https://uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.js",
        //"//uicdn.toast.com/tui-image-editor/v3.10.0/tui-image-editor.js"
        'https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.js'
    ];



//    public $depends = [
//        JqueryAsset::class,
//        BootstrapGroupAsset::class
//    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

}