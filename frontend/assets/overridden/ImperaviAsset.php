<?php


namespace frontend\assets\overridden;

use vova07\imperavi\Asset;

class ImperaviAsset extends Asset
{
    public $sourcePath = '@vendor/vova07/yii2-imperavi-widget/src/assets';
    public $baseUrl = '@web';

    public $css = [
        'redactor.css',
        'plugins/clips/clips.css'
    ];

    public $js = [
        'redactor.js',
        'plugins/clips/clips.js',
        'plugins/fullscreen/fullscreen.js',
    ];

    public function addPlugins($plugins)
    {
    }

}
