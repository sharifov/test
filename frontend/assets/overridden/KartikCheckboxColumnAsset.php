<?php


namespace frontend\assets\overridden;

use kartik\grid\CheckboxColumnAsset;
use yii\helpers\ArrayHelper;

class KartikCheckboxColumnAsset extends CheckboxColumnAsset
{
    public $bsDependencyEnabled = false;
    public $bsPluginEnabled = false;

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
