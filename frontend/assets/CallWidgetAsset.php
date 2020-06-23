<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * CallWidget asset bundle.
 *
 * @author Alex Connor <alex.connor@techork.com>
 * @since 1.0
 */
class CallWidgetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /*public $css = [
        'css/site.css',
    ];*/

    public $js = [
        ['/js/call-widget/call-tab.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/js/call-widget/contacts-tab.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
        ['/js/call-widget/history-tab.jsx', 'position' => \yii\web\View::POS_HEAD, 'type' => 'text/babel'],
    ];

    public $depends = [
        ReactAsset::class
    ];
}