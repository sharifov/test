<?php


namespace frontend\assets\overridden;

use kartik\editable\EditableAsset;
use yii\helpers\ArrayHelper;

class KartikEditablePjaxAsset extends \kartik\editable\EditablePjaxAsset
{
    public $sourcePath = '@vendor/kartik-v/yii2-editable/src/assets';
    public $baseUrl = '@web';

    private $excludeAsset = [
        EditableAsset::class
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
