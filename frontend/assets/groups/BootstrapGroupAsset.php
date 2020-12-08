<?php


namespace frontend\assets\groups;

use frontend\themes\gentelella_v2\assets\BootstrapProgressbar;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\JqueryAsset;

class BootstrapGroupAsset extends \yii\web\AssetBundle
{
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        BootstrapProgressbar::class,
//        PopoverXAsset::class
    ];
}
