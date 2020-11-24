<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use rmrevin\yii\fontawesome\NpmFreeAssetBundle;
use kivork\bootstrap4glyphicons\assets\GlyphiconAsset;
use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella_v2/font-awesome/';
    public $css = [
        'font-awesome.min.css',
    ];

//    public $depends = [
//        FontAwesomeAllAsset::class,
//        GlyphiconAsset::class,
//    ];
}
