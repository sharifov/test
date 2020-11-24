<?php


namespace frontend\themes\gentelella_v2\assets\groups;

use frontend\assets\CentrifugeAsset;
use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\AllSharedDependenciesAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAllAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAsset;
use frontend\widgets\notification\NotificationSocketAsset;
use yii\web\YiiAsset;
use yii\widgets\PjaxAsset;

class GentelellaCrudAsset extends \yii\web\AssetBundle
{
    public $depends = [
        FontAwesomeAllAsset::class,
        FontAwesomeAsset::class,
        AllSharedDependenciesAsset::class,
        AllSharedGroupAsset::class,
        CentrifugeAsset::class,
        NotificationSocketAsset::class,
        GentelellaCrudGroupAsset::class
    ];
}
