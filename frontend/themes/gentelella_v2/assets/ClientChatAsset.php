<?php
namespace frontend\themes\gentelella_v2\assets;

use yii\web\AssetBundle;

class ClientChatAsset extends AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/';
    public $css = [
        'css/client-chat/client-chat.css?v1.0',
    ];

    public $js = [
        'js/client-chat/client-chat.js'
    ];

    public $depends = [
        FontAwesomeAsset::class,
    ];
}
