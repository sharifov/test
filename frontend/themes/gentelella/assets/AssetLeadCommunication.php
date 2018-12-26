<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella\assets;

class AssetLeadCommunication extends \yii\web\AssetBundle
{
    //public $sourcePath = '@frontend/themes/gentelella/';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style-lead-communication.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js'
        //'js/js-lead-communication.js',
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
