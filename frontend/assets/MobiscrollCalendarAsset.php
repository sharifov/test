<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class MobiscrollCalendarAsset
 */
class MobiscrollCalendarAsset extends AssetBundle
{
    public $css = [
        '/js/mobiscroll/css/mobiscroll.jquery.min.css',
    ];

    public $js = [
        '/js/mobiscroll/js/mobiscroll.jquery.min.js',
        'https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment-with-locales.min.js',
        'https://cdn.jsdelivr.net/npm/moment-timezone-all@0.5.5/builds/moment-timezone-with-data.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
