<?php
namespace frontend\assets\groups;

use yii\web\AssetBundle;

class AllSharedGroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        ['js/pnotify/pnotify.custom.min.css'],
    ];

    public $js = [
        ['js/pnotify/pnotify.https.custom.min.js'],

        ['js/init-objects.js'],

        ['/js/page-loader.js',]
    ];
}