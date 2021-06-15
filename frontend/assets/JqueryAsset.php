<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the [jQuery](http://jquery.com/) JavaScript library.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JqueryAsset extends AssetBundle
{
    //public $sourcePath = '@bower/jquery/dist';
    public $sourcePath = null;
//    public $js = [
//        'jquery.js',
//    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js',
        //'https://code.jquery.com/jquery-3.6.0.min.js',
    ];
}
