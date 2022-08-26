<?php

namespace frontend\assets;

use modules\featureFlag\FFlag;

class MonitorCallIncomingAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/monitor/call-incoming.css'
    ];

    public $depends = [
        VueAsset::class
    ];

    public function init()
    {
        $this->js[] = 'js/monitor/call-incoming-vue.js';
    }
}
