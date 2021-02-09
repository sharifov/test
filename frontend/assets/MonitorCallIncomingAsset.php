<?php

namespace frontend\assets;

class MonitorCallIncomingAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $js = [
        'js/monitor/call-incoming.js'
    ];

    public $css = [
        'css/monitor/call-incoming.css'
    ];

    public $depends = [
        VueAsset::class
    ];
}
