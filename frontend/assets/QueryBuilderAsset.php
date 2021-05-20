<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Alexandr
 * @since 1.0
 */
class QueryBuilderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        //'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/docs/custom_theme/css/base.css',
        'https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6/dist/css/query-builder.dark.min.css',
    ];

    public $js = [
        //'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js',
        'https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6/dist/js/query-builder.standalone.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/interact.js/1.10.11/interact.min.js',
        //'https://cdn.jsdelivr.net/npm/interact-js@2.1.0/interact.min.js',
    ];

    public $depends = [

        //JqueryAsset::class,
        //BootstrapAsset::class,

        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => View::POS_END
    ];
}
