<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella\assets;

use yii\web\AssetBundle;

class ThemeAsset extends AssetBundle
{
    public $sourcePath = '@bower/gentelella/build/';
    public $css = [
        'css/custom.css',
        //'https://use.fontawesome.com/releases/v5.4.1/css/all.css',
    ];
    public $js = [
        //'js/custom.js',
    ];
    public $depends = [
        //'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'yiister\gentelella\assets\BootstrapProgressbar',
    ];
}
