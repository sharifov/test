<?php


namespace frontend\themes\gentelella_v2\assets\groups;

use frontend\assets\CentrifugeAsset;
use frontend\assets\groups\AllSharedDependenciesAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAsset;
use frontend\widgets\notification\NotificationSocketAsset;

class GentelellaCrudAsset extends \yii\web\AssetBundle
{
    public $depends = [
        FontAwesomeAsset::class,
        AllSharedDependenciesAsset::class,
        AllSharedGroupAsset::class,
        CentrifugeAsset::class,
        NotificationSocketAsset::class,
        GentelellaCrudGroupAsset::class
    ];
}
