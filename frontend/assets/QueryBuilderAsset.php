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
        //'js/qbuilder/dist/css/query-builder.dark.min.css'
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/2.0.0-beta1/css/bootstrap-select.min.css',
        'https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6/dist/css/query-builder.dark.min.css',
    ];

    public $js = [
        //'js/qbuilder/dist/js/query-builder.standalone.min.js',
        //'https://code.jquery.com/jquery-3.5.1.slim.min.js',
        //'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js',

        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/2.0.0-beta1/js/bootstrap-select.min.js',
        'https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.6/dist/js/query-builder.standalone.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/interact.js/1.10.11/interact.min.js',
        //'https://cdn.jsdelivr.net/npm/interact-js@2.1.0/interact.min.js',



    ];

    public $depends = [
        //'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => View::POS_END
    ];
}
