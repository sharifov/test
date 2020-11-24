<?php


namespace frontend\themes\gentelella_v2\assets\groups;

use frontend\assets\CentrifugeAsset;
use frontend\assets\groups\AllSharedDependenciesAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAllAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAsset;
use frontend\themes\gentelella_v2\assets\SentryAsset;
use frontend\widgets\notification\NotificationSocketAsset;

class GentelellaAsset extends \yii\web\AssetBundle
{
    public $depends = [
        FontAwesomeAllAsset::class,
        FontAwesomeAsset::class,
        AllSharedDependenciesAsset::class,
        AllSharedGroupAsset::class,
        GentelellaGroupAsset::class,
        SentryAsset::class,
        CentrifugeAsset::class,
        NotificationSocketAsset::class,
    ];
}
