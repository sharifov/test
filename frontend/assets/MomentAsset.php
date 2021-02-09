<?php


namespace frontend\assets;

class MomentAsset extends \yii\web\AssetBundle
{
    public $sourcePath;
    public $basePath;
    public $baseUrl;

    public $js = [
//        '/js/moment.min.js',
        'https://cdn.jsdelivr.net/npm/moment@2.29/moment.min.js'
    ];
}
