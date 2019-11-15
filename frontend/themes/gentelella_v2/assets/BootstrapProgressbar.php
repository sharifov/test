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
	public $sourcePath = '@frontend/themes/gentelella_v2/';
	public $baseUrl = '@web';
	public $css = [
    	'css/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css'
	];
    public $js = [
        'js/bootstrap-progressbar/bootstrap-progressbar.min.js',
    ];
    public $depends = [
		BootstrapPluginAsset::class,
    ];
}