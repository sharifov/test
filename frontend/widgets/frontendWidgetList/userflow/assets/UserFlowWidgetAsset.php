<?php

namespace frontend\widgets\frontendWidgetList\userflow\assets;

class UserFlowWidgetAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/widgets/frontendWidgetList/userflow/src/';
    public $baseUrl = '@web';

    public $js = [
        'js/userflow.js',
    ];
}