<?php

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Class UserShiftCalendarAsset
 */
class UserShiftCalendarAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';

    public function init()
    {
        parent::init();
        $version = Yii::$app->params['release']['version'] ?? '' ;
        $this->css[] = ['/css/style-user-shift-calendar.css?v=' . $version];
    }

//    public $css = [
//        // '/css/style-user-shift-calendar.css',
//    ];
//
//    public $js = [
//        // '/js/mobiscroll/js/mobiscroll.jquery.min.js',
//    ];

    public $depends = [
        MobiscrollCalendarAsset::class,
    ];
}
