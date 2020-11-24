<?php


namespace frontend\assets\overridden;

use kartik\popover\PopoverXAsset;
use yii\helpers\ArrayHelper;

class KartikEditableAsset extends \kartik\editable\EditableAsset
{
    public $sourcePath = '@vendor/kartik-v/yii2-editable/src/assets';
    public $baseUrl = '@web';

    private $excludeAsset = [
        PopoverXAsset::class
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
