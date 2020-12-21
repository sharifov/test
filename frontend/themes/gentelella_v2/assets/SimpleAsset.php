<?php


namespace frontend\themes\gentelella_v2\assets;

use frontend\assets\CentrifugeAsset;
use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\AllSharedDependenciesAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaGroupAsset;
use yii\web\JqueryAsset;

class SimpleAsset extends \yii\web\AssetBundle
{
    public $depends = [
        JqueryAsset::class,
        BootstrapGroupAsset::class,
        AllSharedAsset::class,
        AllSharedDependenciesAsset::class,
        AllSharedGroupAsset::class,
        FontAwesomeAsset::class,
        GentelellaGroupAsset::class,
        SentryAsset::class,
        CentrifugeAsset::class
    ];
}
