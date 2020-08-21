<?php

namespace frontend\widgets\newWebPhone;

use frontend\assets\ReactAsset;
use frontend\assets\WebPhoneAsset;
use yii\web\AssetBundle;

class NewWebPhoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        WebPhoneAsset::class,
        ReactAsset::class
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

    public function init()
    {
        if (YII_ENV === 'prod' || YII_ENV === 'stage') {
            $version = \Yii::$app->params['release']['version'] ?? '';
            $this->js = [
                'https://cdn.jsdelivr.net/npm/simplebar@5.2.1/dist/simplebar.min.js',
                'web_phone/widget-min.js?v=' . $version,
            ];

            $this->css = [
                'https://cdn.jsdelivr.net/npm/simplebar@5.2.1/dist/simplebar.css',
                'web_phone/widget.css?v=' . $version,
            ];

        } else {
            $this->js = [
                'https://cdn.jsdelivr.net/npm/simplebar@5.2.1/dist/simplebar.min.js',

                ['/web_phone/js/init.js'],
                ['/web_phone/js/event_dispatcher.js'],
                ['/web_phone/js/events.js'],
                ['/web_phone/js/call_object.js'],
                ['/web_phone/js/conference_object.js'],
                ['/web_phone/js/requesters.js'],
                ['/web_phone/js/old_widget.js'],

                ['/web_phone/component/timer.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
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
                ['/web_phone/component/pane/conference/pane.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
                ['/web_phone/component/pane/add_note.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],

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

                '/web_phone/js/phone-widget.js',

                '/web_phone/js/status.js',
                '/web_phone/js/call.js',
                '/web_phone/js/sms.js',
                '/web_phone/js/contacts.js',
                '/web_phone/js/email.js',

                '/web_phone/js/test.js',
            ];

            $this->css = [
                'https://cdn.jsdelivr.net/npm/simplebar@5.2.1/dist/simplebar.css',
                'css/style-web-phone-new.css',
                'css/additional-styles.css'
            ];
        }
        parent::init();
    }
}
