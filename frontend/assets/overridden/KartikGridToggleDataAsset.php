<?php


namespace frontend\assets\overridden;

use kartik\grid\GridToggleDataAsset;
use yii\helpers\ArrayHelper;

class KartikGridToggleDataAsset extends GridToggleDataAsset
{
    public $bsPluginEnabled = false;
    public $bsDependencyEnabled = false;

    private $excludeAsset = [
        "kartik\\grid\\GridViewAsset"
    ];

    public function init()
    {
        parent::init();
        foreach ($this->excludeAsset as $item) {
            ArrayHelper::removeValue($this->depends, $item);
        }
    }
}
