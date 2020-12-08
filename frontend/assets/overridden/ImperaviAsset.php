<?php


namespace frontend\assets\overridden;

use yii\helpers\VarDumper;

class ImperaviAsset extends \vova07\imperavi\Asset
{
    private $pluginsCss = [
        'clips',
    ];

    private $pluginsJs = [
        'fullscreen'
    ];

    public $sourcePath = '@vendor/vova07/yii2-imperavi-widget/src/assets';
    public $baseUrl = '@web';

    public function addPlugins($plugins)
    {
        parent::addPlugins($plugins);
    }

    public function init()
    {
        parent::init();
        if (is_array($this->css)) {
            $this->addPlugins($this->pluginsCss);
        }

        if (is_array($this->js)) {
            $this->addPlugins($this->pluginsJs);
        }
    }
}
