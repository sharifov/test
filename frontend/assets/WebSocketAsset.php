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
class WebSocketAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        /*'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.mobile.css',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.css',*/
        'js/pnotify/pnotify.custom.min.css'
    ];

    public $js = [
        'js/pnotify/pnotify.custom.min.js',
        //'https://cdnjs.cloudflare.com/ajax/libs/ion-sound/3.0.7/js/ion.sound.min.js'
        /*'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.desktop.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.animate.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.mobile.js'*/
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
