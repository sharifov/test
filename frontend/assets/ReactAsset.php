<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * React application asset bundle.
 *
 * @author Alex Connor <alex.connor@techork.com>
 * @since 1.0
 */
class ReactAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /*public $css = [
        'css/site.css',
    ];*/

    /*public $js = [
        ['https://cdn.jsdelivr.net/npm/vue/dist/vue.js', 'position' => \yii\web\View::POS_HEAD],
    ];*/

    public function init()
    {
        parent::init();

        if (YII_ENV === 'prod' || YII_ENV === 'stage') {
            $this->js[] = ['https://unpkg.com/react@16/umd/react.production.min.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true];
            $this->js[] = ['https://unpkg.com/react-dom@16/umd/react-dom.production.min.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true];
        } else {
            $this->js[] = ['https://unpkg.com/react@16/umd/react.development.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true];
            $this->js[] = ['https://unpkg.com/react-dom@16/umd/react-dom.development.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true];
        }

        $this->js[] = ['https://unpkg.com/babel-standalone@6/babel.min.js'];

    }

//    public $depends = [
//    ];
}