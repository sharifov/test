<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SlickCarouselAssets extends AssetBundle
{
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick-theme.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick.min.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}