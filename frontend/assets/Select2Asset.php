<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\bootstrap4\BootstrapAsset;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

/**
 * @author Alexandr
 * @since 1.0
 */
class Select2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css'
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/en.js',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.full.min.js',
    ];

    public $depends = [
        YiiAsset::class,
        JqueryAsset::class,
        BootstrapAsset::class,
    ];

    /*public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];*/
}
