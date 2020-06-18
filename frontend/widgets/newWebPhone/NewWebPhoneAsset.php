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
		'css/additional-styles.css'
	];

	public $js = [
		'https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js',

        '/web_phone/js/pane/active/btn/add_person.js',
        '/web_phone/js/pane/active/btn/dialpad.js',
        '/web_phone/js/pane/active/btn/hangup.js',
        '/web_phone/js/pane/active/btn/hold.js',
        '/web_phone/js/pane/active/btn/mute.js',
        '/web_phone/js/pane/active/btn/transfer.js',
        '/web_phone/js/pane/active/pane.js',

        '/web_phone/tpl/incoming.js',
        '/web_phone/js/pane/incoming/pane.js',

        '/web_phone/js/pane/outgoing/pane.js',

        '/web_phone/js/queue.js',

		'/js/phone-widget.js',
		'/web_phone/js/status.js',
		'/web_phone/js/call.js',
		'/web_phone/js/sms.js',
		'/web_phone/js/contacts.js',
		'/web_phone/js/email.js',
	];

	public $depends = [
		WebPhoneAsset::class
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_END
	];
}
