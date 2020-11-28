<?php
namespace frontend\themes\gentelella_v2\assets;

use frontend\assets\MomentAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ClientChatAsset extends AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/';
    public $css = [
        'css/client-chat/client-chat.css',
    ];

    public $js = [
        'js/client-chat/client-chat.js',
        'js/client-chat/horsey.min.js'
    ];

    public $depends = [
        JqueryAsset::class,
        FontAwesomeAsset::class,
    ];
}
