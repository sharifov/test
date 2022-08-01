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
        /** @fflag FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE, Switch incoming monitor page to new version */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE)) {
            $this->js[] = 'js/monitor/call-incoming-vue.js';
        } else {
            $this->js[] = 'js/monitor/call-incoming.js';
        }
    }
}
