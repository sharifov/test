<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace backend\themes\gentelella\assets;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@backend/themes/gentelella/';
    public $css = [
        'css/custom2.css',
    ];

    public $js = [
        'js/custom.js',
    ];

    /*public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
        'yiister\gentelella\assets\Asset',
    ];*/

    public $depends = [
        'backend\themes\gentelella\assets\ThemeAsset',
        'yiister\gentelella\assets\ExtensionAsset',
    ];

}
