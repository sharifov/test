<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\web\JqueryAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * @author Alexandr
 * @since 1.0
 */
class WebPhoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style-web-phone.css'
    ];

    public $depends = [
        YiiAsset::class,
        JqueryAsset::class,
        BootstrapAsset::class,
        TwilioAsset::class
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
