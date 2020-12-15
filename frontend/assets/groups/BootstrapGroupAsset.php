<?php


namespace frontend\assets\groups;

use yii\web\JqueryAsset;

class BootstrapGroupAsset extends \yii\web\AssetBundle
{
    public $sourcePath = null;
    public $basePath = null;

    public $css = ['https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css'];

    public $js = ['https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js'];

    public $depends = [
        JqueryAsset::class,
    ];
}
