<?php


namespace frontend\assets\overridden;

use kartik\dialog\DialogAsset;
use yii\helpers\ArrayHelper;

class KartikGridExportAsset extends \kartik\grid\GridExportAsset
{
    public $bsDependencyEnabled = false;
    public $bsPluginEnabled = false;

    private $excludeAsset = [
        DialogAsset::class
    ];

    public function init()
    {
        parent::init();
        foreach ($this->excludeAsset as $item) {
            ArrayHelper::removeValue($this->depends, $item);
        }
    }
}
