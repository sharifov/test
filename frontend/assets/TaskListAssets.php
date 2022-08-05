<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class TaskListAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/css/style-task-list.css',
    ];
    public $js = [
        '/js/task-list/tasklist.js',
    ];

    public $depends = [
       // SlickCarouselAssets::class,
    ];
}
