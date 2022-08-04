<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class TaskListAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [

    ];

    public $depends = [
        SlickCarouselAssets::class,
    ];
}
