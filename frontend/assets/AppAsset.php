<?php

namespace frontend\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\themes\gentelella_v2\assets\BootstrapProgressbar;
use frontend\themes\gentelella_v2\assets\groups\GentelellaAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $depends = [
        JqueryAsset::class,
        BootstrapGroupAsset::class,
        BootstrapProgressbar::class,
        AllSharedAsset::class,
        PageLoaderAsset::class,
        GentelellaAsset::class,
    ];
}
