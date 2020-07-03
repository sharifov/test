<?php

namespace frontend\widgets\newWebPhone;

use frontend\assets\ReactAsset;
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

        ['/web_phone/js/init.js'],
        ['/web_phone/js/call_object.js'],

        ['/web_phone/component/pane/call_action_timer.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/active/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/incoming/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/active/controls.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/call_info.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/list_item.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/groups.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/group_item.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/queues.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/outgoing/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/contact_info.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],

        '/web_phone/tpl/contact.js',
        '/web_phone/js/contact_info.js',
        '/web_phone/js/dialpad.js',

        '/web_phone/js/pane/active/btn/btn.js',
        '/web_phone/js/pane/active/btn/add_person.js',
        '/web_phone/js/pane/active/btn/dialpad.js',
        '/web_phone/js/pane/active/btn/hold.js',
        '/web_phone/js/pane/active/btn/mute.js',
        '/web_phone/js/pane/active/btn/transfer.js',
        '/web_phone/js/pane/active/pane.js',

        '/web_phone/tpl/incoming.js',
        '/web_phone/js/pane/incoming/pane.js',

        '/web_phone/tpl/outgoing.js',
        '/web_phone/js/pane/outgoing/pane.js',

        '/web_phone/js/pane/queue/pane.js',

        '/web_phone/js/queue/queue.js',
        '/web_phone/js/queue/test.js',

		'/js/phone-widget.js',
		'/web_phone/js/status.js',
		'/web_phone/js/call.js',
		'/web_phone/js/sms.js',
		'/web_phone/js/contacts.js',
		'/web_phone/js/email.js',
	];

	public $depends = [
		WebPhoneAsset::class,
        ReactAsset::class
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_END
	];
}
