<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/';
    public $baseUrl = '@web';

    public $css = [
        'css/style-req.css?v=1.1'
    ];

    public $js = [
        [
            'https://js.sentry-cdn.com/759a9e865aaa4088acd6fb21376c5289.min.js',
            'crossorigin' => 'anonymous',
            'data-lazy' => 'no',
            'position' => \yii\web\View::POS_HEAD,
        ],
        'js/util.js',
        'js/extension.js',
        '/js/centrifuge-js-master/dist/centrifuge.js',
    ];

    public $depends = [
        ThemeAsset::class,
        CommonAsset::class
//        'yiister\gentelella\assets\ExtensionAsset',
    ];
}
