<?php


namespace frontend\assets\overridden;

use yii\web\AssetBundle;

class KDNJsonEditorAsset extends AssetBundle
{
    public $sourcePath = '@npm/jsoneditor/dist';

    public $css = [
        'jsoneditor.css',
    ];

    public $js = [
        'jsoneditor.js',
    ];

}
