<?php
namespace frontend\assets\groups;

use yii\web\AssetBundle;

class AllSharedGroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        ['js/pnotify/pnotify5.custom.min.css'],
    ];

    public $js = [
        ['js/pnotify/pnotify5.custom.min.js'],
        ['js/pnotify/pnotify5.bootstrap4.min.js'],
        ['js/pnotify/pnotify5.fontawesome.min.js'],
        ['js/pnotify/pnotify5.desktop.min.js'],
        ['js/pnotify/pnotify5.paginate.min.js'],

        ['js/init-objects.js'],

//        ['/js/page-loader.js',]
    ];
}