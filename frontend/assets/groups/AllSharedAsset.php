<?php


namespace frontend\assets\groups;

use frontend\themes\gentelella_v2\assets\BootstrapProgressbar;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\widgets\PjaxAsset;

class AllSharedAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        JqueryAsset::class,
        YiiAsset::class,
        PjaxAsset::class,
        BootstrapProgressbar::class,
    ];
}
