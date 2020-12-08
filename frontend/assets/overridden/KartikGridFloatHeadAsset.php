<?php


namespace frontend\assets\overridden;

use yii\helpers\ArrayHelper;

class KartikGridFloatHeadAsset extends \kartik\grid\GridFloatHeadAsset
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
