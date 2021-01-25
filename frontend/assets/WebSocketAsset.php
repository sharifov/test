<?php

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * @author Alexandr
 * @since 1.1
 */
class WebSocketAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];


//    public $js = [
//        //'js/pnotify/pnotify.custom.min.js',
//        '/js/reconnecting-websocket.min.js',
//        '/js/websocket-commands.js'
//    ];

    public function init()
    {
        parent::init();

        $version = Yii::$app->params['release']['version'] ?? '' ;
        $this->js[] = ['/js/reconnecting-websocket.min.js'];
        $this->js[] = ['/js/websocket-commands.js?v=' . $version];
    }

//    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\web\JqueryAsset',
//        'yii\bootstrap4\BootstrapAsset',
//    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
