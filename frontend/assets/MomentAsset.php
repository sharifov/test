<?php


namespace frontend\assets;

class MomentAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/moment.min.js',
    ];
}
