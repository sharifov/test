<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ImageEditorAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'https://uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.css',
        'https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.css'
    ];
    public $js = [
        'https://uicdn.toast.com/tui.code-snippet/latest/tui-code-snippet.js',
        'https://uicdn.toast.com/tui-color-picker/v2.2.6/tui-color-picker.js',
        'https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.js'
    ];

    public $depends = [
        FabricAsset::class,
        FileSaverAsset::class
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

}