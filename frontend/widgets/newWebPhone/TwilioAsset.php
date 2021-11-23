<?php

namespace frontend\widgets\newWebPhone;

class TwilioAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'twilio/twilio-2.0.1.js',
    ];

    public $depends = [
        NewWebPhoneAsset::class,
    ];
}
