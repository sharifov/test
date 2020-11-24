<?php


namespace frontend\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaCrudAsset;

class AppCrudAsset extends \yii\web\AssetBundle
{
    public $depends = [
        AllSharedAsset::class,
        GentelellaCrudAsset::class,
    ];
}
