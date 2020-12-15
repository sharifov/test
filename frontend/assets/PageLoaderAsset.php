<?php
/**
 * @copyright Copyright (c) 2020 Kivork
 */

namespace frontend\assets;

use frontend\assets\groups\BootstrapGroupAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

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
        JqueryAsset::class,
        BootstrapGroupAsset::class
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
