<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class NewWebPhoneAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
		'https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css',
		'css/style-web-phone-new.css',
	];

	public $js = [
		'https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js',
		'/js/phone-widget.js'
	];

	public $depends = [
		WebPhoneAsset::class
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_END
	];
}