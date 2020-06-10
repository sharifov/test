<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Vue application asset bundle.
 *
 * @author Alex Connor <alex.connor@techork.com>
 * @since 1.0
 */
class VueAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /*public $css = [
        'css/site.css',
    ];*/

    public $js = [
        ['https://cdn.jsdelivr.net/npm/vue/dist/vue.js', 'position' => \yii\web\View::POS_HEAD],
    ];

    /*public function init()
    {
        parent::init();

        $this->js[] = YII_ENV === 'dev' ? 'app.js' : 'app.min.js';
    }*/

    public $depends = [
    ];
}