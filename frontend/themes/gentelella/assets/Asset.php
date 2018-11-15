<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella\assets;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@frontend/themes/gentelella/';
    //public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        //'css/custom2.css',
        'css/style-req.css?v=1.9',
        //'https://use.fontawesome.com/releases/v5.4.1/css/all.css',
        //'css/font-awesome.css',
        //'css/style.css?v=2',
        //'css/site.css',
    ];

    public $js = [
        'js/custom.js',
        'js/util.js'
    ];

    /*public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
        'yiister\gentelella\assets\Asset',
    ];*/

    public $depends = [
        'frontend\themes\gentelella\assets\ThemeAsset',
        'yiister\gentelella\assets\ExtensionAsset',
    ];

}
