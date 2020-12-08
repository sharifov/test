<?php


namespace frontend\assets\groups;

use yii\web\AssetBundle;

class AllSharedDependenciesAsset extends \yii\web\AssetBundle
{
    public $sourcePath = null;
    public $basePath = null;

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/favico.js/0.3.10/favico.min.js',
    ];

    public $depends = [];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}
