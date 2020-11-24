<?php


namespace frontend\assets\groups;


use yii2mod\rbac\RbacAsset;
use yii2mod\rbac\RbacRouteAsset;

class RbacGroupAsset extends \yii\web\AssetBundle
{
    public $depends = [
        RbacAsset::class,
        RbacRouteAsset::class
    ];
}