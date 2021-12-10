<?php

namespace frontend\widgets\newWebPhone;

use yii\web\AssetBundle;

class NewWebPhoneGroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [

        ['/web_phone/js/init.js'],
        ['/web_phone/js/event_dispatcher.js'],
        ['/web_phone/js/events.js'],
        ['/web_phone/js/call_object.js'],
        ['/web_phone/js/conference_object.js'],
        ['/web_phone/js/requesters.js'],

        ['/web_phone/component/timer.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/call_action_timer.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/active/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/incoming/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/active/controls.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/call_info.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/list_item.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/priority_item.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/groups.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/group_item.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/queue/queues.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/outgoing/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/contact_info.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/conference/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/pane/add_note.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/web_phone/component/notification/notifications.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],

        '/web_phone/js/contact_info.js',
        '/web_phone/js/dialpad.js',

        '/web_phone/js/pane/active/btn/btn.js',
        '/web_phone/js/pane/active/btn/hold.js',
        '/web_phone/js/pane/active/btn/mute.js',

        '/web_phone/js/pane/active/pane.js',
        '/web_phone/js/pane/incoming/pane.js',
        '/web_phone/js/pane/outgoing/pane.js',
        '/web_phone/js/pane/queue/pane.js',

        '/web_phone/js/queue/queue.js',
        '/web_phone/js/storage/conference.js',

        '/web_phone/js/notifier/notifier.js',

        '/web_phone/js/phone-widget.js',

        '/web_phone/js/audio.js',

        '/web_phone/js/logger.js',
        '/web_phone/js/device_state.js',
        '/web_phone/js/status.js',
        '/web_phone/js/call.js',
        '/web_phone/js/sms.js',
        '/web_phone/js/contacts.js',
        '/web_phone/js/email.js',

        '/web_phone/js/test.js',

    ];

    public $css = [
        'css/style-web-phone-new.css',
        'css/additional-styles.css'
    ];
}
