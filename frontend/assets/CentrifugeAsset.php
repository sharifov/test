<?php


namespace frontend\assets;

class CentrifugeAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/centrifuge-js-master/dist/centrifuge.js'
    ];
}
