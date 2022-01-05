<?php

namespace frontend\widgets\newWebPhone;

class DeviceAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'twilio/twilio-2.0.1.js',
        'web_phone/device/logLevel.js',
        'web_phone/device/remote_logger.js',
        'web_phone/device/device.js',
    ];

    public $depends = [
        NewWebPhoneAsset::class,
    ];
}
