<?php

namespace frontend\widgets\clientChat;

use frontend\assets\MomentAsset;
use yii\web\AssetBundle;

class ClientChatWidgetAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/clientChat/assets/';

    public $css = [
        'css/_client-chat.css'
    ];

    public $js = [
        'js/chat.js',
        'js/datastore.js',
        'js/_client-chat.js',
    ];

    public $depends = [
        MomentAsset::class
    ];
}
