<?php

namespace frontend\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\jui\JuiAsset;
use yii\bootstrap4\BootstrapPluginAsset;

/**
 * Class FormBuilderAsset
 */
class FormBuilderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/jQuery-formBuilder/3.6.1/form-builder.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jQuery-formBuilder/3.6.1/form-render.min.js',
    ];

    public $depends = [
        YiiAsset::class,
        JqueryAsset::class,
        BootstrapAsset::class,
        JuiAsset::class,
        BootstrapPluginAsset::class,
    ];
}
