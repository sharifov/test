<?php


namespace frontend\assets\overridden;

use kartik\dialog\DialogAsset;
use yii\helpers\ArrayHelper;

class KartikDialogBootstrapAsset extends \kartik\dialog\DialogBootstrapAsset
{
    private $excludeAsset = [
        DialogAsset::class
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
