<?php
namespace frontend\widgets\clientChat;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class ClientChatAsset extends AssetBundle
{
	public $sourcePath = '@frontend/widgets/clientChat/';

	public $css = [
		'css/_client-chat.css'
	];

	public $js = [
		'js/_client-chat.js'
	];

	public $depends = [
		YiiAsset::class,
	];
}