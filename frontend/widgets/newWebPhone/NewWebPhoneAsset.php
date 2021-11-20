<?php

namespace frontend\widgets\newWebPhone;

use frontend\assets\ReactAsset;
use frontend\assets\SimpleBarAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class NewWebPhoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

    public $css = [
        'css/style-web-phone.css'
    ];

    public $depends = [
        YiiAsset::class,
        JqueryAsset::class,
        BootstrapAsset::class,
        ReactAsset::class,
        SimpleBarAsset::class,
        NewWebPhoneGroupAsset::class,
    ];
}
