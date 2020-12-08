<?php


namespace frontend\assets\overridden;

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveFormAsset;

class KartikActiveFormAsset extends \kartik\form\ActiveFormAsset
{
    public $sourcePath = '@vendor/kartik-v/yii2-widget-activeform/src/assets';
    public $baseUrl = '@web';

    private $excludeAsset = [
        ActiveFormAsset::class
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
