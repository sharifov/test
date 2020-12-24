<?php


namespace frontend\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\themes\gentelella_v2\assets\BootstrapProgressbar;
use frontend\themes\gentelella_v2\assets\groups\GentelellaCrudAsset;
use yii\web\JqueryAsset;

class AppCrudAsset extends \yii\web\AssetBundle
{
    public $depends = [
        JqueryAsset::class,
        BootstrapGroupAsset::class,
        BootstrapProgressbar::class,
        AllSharedAsset::class,
        PageLoaderAsset::class,
        GentelellaCrudAsset::class,
    ];
}
