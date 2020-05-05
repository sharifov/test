<?php
namespace frontend\widgets\newWebPhone;

use frontend\assets\WebPhoneAsset;
use yii\web\AssetBundle;

class NewWebPhoneAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
		'https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css',
		'css/style-web-phone-new.css',
        '/web_phone/css/sms.css',
	];

	public $js = [
		'https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js',
		'/js/phone-widget.js',
		'/web_phone/js/sms.js',
	];

	public $depends = [
		WebPhoneAsset::class
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_END
	];
}