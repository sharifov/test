<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use yii\web\AssetBundle;
use yii\bootstrap4\BootstrapPluginAsset;

class BootstrapProgressbar extends AssetBundle
{
    public $sourcePath = null;
    public $baseUrl = null;
    public $css = [
//        'css/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css'
        'https://cdn.jsdelivr.net/npm/bootstrap-progressbar@0.9.0/css/bootstrap-progressbar-3.3.4.min.css'
    ];
    public $js = [
//        'js/bootstrap-progressbar/bootstrap-progressbar.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-progressbar/0.9.0/bootstrap-progressbar.min.js'
    ];
    public $depends = [
        BootstrapPluginAsset::class,
    ];
}
