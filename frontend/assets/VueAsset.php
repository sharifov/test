<?php

namespace frontend\assets;

use frontend\themes\gentelella_v2\assets\SimpleAsset;
use modules\featureFlag\FFlag;
use yii\web\AssetBundle;

/**
 * Vue application asset bundle.
 *
 * @author Alex Connor <alex.connor@techork.com>
 * @since 1.0
 */
class VueAsset extends AssetBundle
{
    public $sourcePath = null;
    public $basePath = null;
    public $baseUrl = null;

//    public $css = [
//        'https://cdn.jsdelivr.net/npm/animate.css@4.1',
//    ];

    /*public $js = [
        ['https://cdn.jsdelivr.net/npm/vue/dist/vue.js', 'position' => \yii\web\View::POS_END],
    ];*/

    public function init()
    {
        parent::init();

        if (YII_ENV === 'prod' || YII_ENV === 'stage') {
            // $jsFile = 'https://cdn.jsdelivr.net/npm/vue';
            $jsFile = 'https://unpkg.com/vue@3.0/dist/vue.global.prod.js';
        } else {
            // $jsFile = 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js';
            $jsFile = 'https://unpkg.com/vue@3.0/dist/vue.global.js';
        }

        //vue(.runtime).global(.prod).js

        $this->js[] = [$jsFile, 'position' => \yii\web\View::POS_BEGIN];
        $this->js[] = ['https://cdn.jsdelivr.net/npm/vue3-sfc-loader', 'position' => \yii\web\View::POS_END];
        $this->js[] = ['https://cdn.jsdelivr.net/npm/axios@0.21/dist/axios.min.js', 'position' => \yii\web\View::POS_END];
        $this->js[] = ['https://cdn.jsdelivr.net/npm/moment@2.29/moment.min.js', 'position' => \yii\web\View::POS_END];

        //$this->js[] = ['https://cdn.jsdelivr.net/npm/moment@2.29/min/moment-with-locales.min.js', 'position' => \yii\web\View::POS_END];
        //$this->js[] = ['https://cdn.jsdelivr.net/npm/moment@2.29/min/locales.min.js', 'position' => \yii\web\View::POS_END];
        $this->js[] = ['https://cdn.jsdelivr.net/npm/moment-timezone@0.5/builds/moment-timezone-with-data.min.js', 'position' => \yii\web\View::POS_END];

        //$this->js[] = ['https://cdnjs.cloudflare.com/ajax/libs/velocity/1.2.3/velocity.min.js', 'position' => \yii\web\View::POS_END];
        //$this->js[] = ['https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js', 'position' => \yii\web\View::POS_END];
        $this->js[] = ['https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.9.6/libphonenumber-js.min.js', 'position' => \yii\web\View::POS_END];
    }
}