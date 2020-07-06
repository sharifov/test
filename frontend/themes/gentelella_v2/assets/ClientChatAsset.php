<?php
namespace frontend\themes\gentelella_v2\assets;

use yii\web\AssetBundle;

class ClientChatAsset extends AssetBundle
{
	public $sourcePath = '@frontend/themes/gentelella_v2/css/client-chat/';
	public $css = [
		'client-chat.css',
	];

	public $depends = [
		FontAwesomeAsset::class,
	];
}