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
class NotifyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        /*'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.mobile.css',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.css',*/
//        'js/pnotify/pnotify.custom.min.css',
//        'js/pnotify/pnotify5.custom.min.css'
        ['js/pnotify/pnotify5.custom.min.css']
    ];

    public $js = [
//        'js/pnotify/pnotify.custom.min.js',
//        'js/pnotify/pnotify.https.custom.min.js',
        ['js/pnotify/pnotify5.custom.min.js'],
        ['js/pnotify/pnotify5.bootstrap4.min.js'],
        ['js/pnotify/pnotify5.fontawesome.min.js'],
        ['js/pnotify/pnotify5.desktop.min.js'],
        ['js/pnotify/pnotify5.paginate.min.js'],
        //'https://cdnjs.cloudflare.com/ajax/libs/ion-sound/3.0.7/js/ion.sound.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/favico.js/0.3.10/favico.min.js',


        /*'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.desktop.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.animate.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.mobile.js',*/

        'js/init-objects.js?v=1.2',
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
