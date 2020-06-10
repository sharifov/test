<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/font-awesome.css',
        'css/style.css?v=1.8.3',
        'css/site.css?v=1.3',
    ];
    public $js = [
        '/js/util.js?v=1.6',
        // ['https://cdn.jsdelivr.net/npm/vue/dist/vue.js', 'position' => \yii\web\View::POS_HEAD],
        //'https://ru.vuejs.org/js/vue.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
