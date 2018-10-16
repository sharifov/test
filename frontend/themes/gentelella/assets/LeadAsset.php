<?php

namespace frontend\themes\gentelella\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LeadAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    //public $sourcePath = '@frontend/themes/gentelella/';
    //public $basePath = '@webroot';


    public $css = [
        //'css/font-awesome.css',
        'css/style-req.css',
        'css/site.css',
    ];
    public $js = [
        '/js/util.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}
