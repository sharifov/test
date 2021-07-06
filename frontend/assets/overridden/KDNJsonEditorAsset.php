<?php


namespace frontend\assets\overridden;

use yii\web\AssetBundle;

class KDNJsonEditorAsset extends AssetBundle
{
    public $sourcePath = null;
    public $basePath = null;

    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.4.1/jsoneditor.min.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.4.1/jsoneditor.min.js',
    ];

}
