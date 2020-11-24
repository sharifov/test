<?php


namespace frontend\assets\overridden;

use yii\helpers\ArrayHelper;

class KartikGridViewAsset extends \kartik\grid\GridViewAsset
{
    private $excludeAsset = [
        "kartik\\dialog\\DialogAsset", "yii\\grid\\GridViewAsset"
    ];

    public $bsDependencyEnabled = false;
    public $bsPluginEnabled = false;

    public function init()
    {
        parent::init();
        foreach ($this->excludeAsset as $item) {
            ArrayHelper::removeValue($this->depends, $item);
        }
    }
}
