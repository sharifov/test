<?php

namespace frontend\widgets\newWebPhone;

use frontend\assets\ReactAsset;
use frontend\assets\SimpleBarAsset;
use frontend\assets\WebPhoneAsset;
use yii\web\AssetBundle;

class NewWebPhoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

    public $depends = [
        WebPhoneAsset::class,
        ReactAsset::class,
        SimpleBarAsset::class,
        NewWebPhoneGroupAsset::class,
    ];
}
