<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use kivork\bootstrap4glyphicons\assets\GlyphiconAsset;
use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
//    public $sourcePath = '@webroot/font-awesome/';
    public $sourcePath = null;
    public $basePath = null;

    public $css = [
//        'font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css'
    ];

    public $depends = [
        FontAwesomeAllAsset::class,
        GlyphiconAsset::class,
    ];
}
