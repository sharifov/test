<?php


namespace frontend\assets\overridden;

use kartik\grid\GridViewAsset;
use yii\helpers\ArrayHelper;

class KartikGridResizeColumnsAsset extends \kartik\grid\GridResizeColumnsAsset
{
    private $excludeAsset = [
        GridViewAsset::class
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
