<?php

namespace frontend\widgets\webPhone;

class TwilioAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'twilio/twilio-2.0.1.js',
    ];
}
