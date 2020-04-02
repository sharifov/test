<?php

namespace frontend\widgets\notification;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class NotificationSocketAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/notification/js';

    public $js = [
        'notification-socket.js',
    ];

    public $depends = [
        YiiAsset::class,
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
