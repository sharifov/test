<?php

namespace frontend\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaAsset;
use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $depends = [
        AllSharedAsset::class,
        GentelellaAsset::class,
    ];
}
