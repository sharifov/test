<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use frontend\assets\CentrifugeAsset;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/src/';
    public $baseUrl = '@web';

    public $css = [
        'css/style-req.css'
    ];

    public $js = [
        'js/util.js',
        'js/extension.js',
    ];

    public $depends = [
        ThemeAsset::class,
        CommonAsset::class,
        SentryAsset::class,
        CentrifugeAsset::class
    ];
}
