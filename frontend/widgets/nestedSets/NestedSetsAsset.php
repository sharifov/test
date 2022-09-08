<?php

namespace frontend\widgets\nestedSets;

use frontend\assets\JqueryAsset;
use frontend\assets\Select2Asset;
use yii\web\AssetBundle;

class NestedSetsAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/nestedSets/assets';

    public $js = [
      'js/select2totree.js',
    ];

    public $css = [
      'css/select2totree.css',
    ];

    public $depends = [
      JqueryAsset::class,
      Select2Asset::class,
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_END];
}
