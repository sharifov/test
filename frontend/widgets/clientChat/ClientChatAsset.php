<?php
namespace frontend\widgets\clientChat;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ClientChatAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/clientChat/';

    public $css = [
        'css/_client-chat.css?v1.0'
    ];

    public $js = [
        'js/chat.js?v1.0',
        'js/datastore.js?v1.0',
        'js/_client-chat.js?v1.0',
    ];

    public $depends = [
        YiiAsset::class,
    ];
}
