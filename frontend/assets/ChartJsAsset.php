<?php


namespace frontend\assets;

use yii\web\AssetBundle;

class ChartJsAsset extends AssetBundle
{
    public $css = [
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js'
    ];
    public $depends = [
    ];
}
