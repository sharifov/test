<?php

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Class UserShiftCalendarAsset
 */
class UserShiftCalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/css/style-user-shift-calendar.css'
    ];

    public $js = [
        '/js/shift/calendar/timeline-multiple-manage.js',
        '/js/shift/calendar/timeline-form-filter.js',
        '/js/shift/calendar/timeline-tooltip.js',
        '/js/shift/calendar/timeline.js',
    ];

    public $depends = [
        MobiscrollCalendarAsset::class,
    ];
}
