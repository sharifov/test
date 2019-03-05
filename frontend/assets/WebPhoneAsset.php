<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Alexandr
 * @since 1.0
 */
class WebPhoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/quickstart.css'
    ];

    public $js = [
        'https://media.twiliocdn.com/sdk/js/client/v1.6/twilio.min.js',
        //'/js/quickstart.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
