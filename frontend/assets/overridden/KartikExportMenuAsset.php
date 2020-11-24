<?php


namespace frontend\assets\overridden;

use kartik\dialog\DialogAsset;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;

class KartikExportMenuAsset extends \kartik\export\ExportMenuAsset
{
    public $bsDependencyEnabled = false;
    public $bsPluginEnabled = false;

    private $excludeAsset = [
        "\\kartik\\dialog\\DialogAsset",
        BootstrapAsset::class
    ];

    public function init()
    {
        parent::init();
        foreach ($this->excludeAsset as $item) {
            ArrayHelper::removeValue($this->depends, $item);
        }
    }
}
