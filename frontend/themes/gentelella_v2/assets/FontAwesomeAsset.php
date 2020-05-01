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
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css',
		'font-awesome.min.css',
	];

	public $depends = [
//		NpmFreeAssetBundle::class,
		GlyphiconAsset::class,
	];
}
