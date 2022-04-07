<?php

namespace frontend\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\bootstrap4\BootstrapPluginAsset;

/**
 * Class FullCalendarAsset
 */
class FullCalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css',

        // 'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.10.2/main.min.css'
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js',
        'https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales-all.min.js',

        // 'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.10.2/main.min.js',
        // 'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.10.2/locales-all.min.js',
    ];

//    public $depends = [
//        YiiAsset::class,
//        JqueryAsset::class,
//        BootstrapAsset::class,
//        JuiAsset::class,
//        BootstrapPluginAsset::class,
//    ];
}
