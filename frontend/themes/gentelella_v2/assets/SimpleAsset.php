<?php


namespace frontend\themes\gentelella_v2\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaGroupAsset;

class SimpleAsset extends \yii\web\AssetBundle
{
    public $depends = [
        AllSharedAsset::class,
        FontAwesomeAsset::class,
        GentelellaGroupAsset::class,
        SentryAsset::class
    ];
}
