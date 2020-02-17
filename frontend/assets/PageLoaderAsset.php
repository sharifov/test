<?php
/**
 * @copyright Copyright (c) 2020 Kivork
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author alex.connor
 * @since 1.0
 */
class PageLoaderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        //'/css/page-loader.css'
    ];

    public $js = [
        '/js/page-loader.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
