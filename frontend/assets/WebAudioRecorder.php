<?php


namespace frontend\assets;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class WebAudioRecorder extends AssetBundle
{
	public $basePath = '@frontend';
	public $baseUrl = '@web';

	public $js = [
		'js/web-audio-recorder/WebAudioRecorder.min.js',
		'js/web-audio-recorder/app.js'
	];

	public $depends = [
		JqueryAsset::class
	];
}