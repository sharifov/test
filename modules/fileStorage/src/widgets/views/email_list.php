<?php

use yii\web\View;
use kartik\select2\Select2;

/** @var View $this */
/** @var array $files */
/** @var string $checkBoxName */
?>

<div class="form-group col-sm-8" style="padding-left: 0">
    <label class="control-label" >Files</label>
    <?= Select2::widget([
        'data' => $files,
        'name' => $checkBoxName . '[files][]',
        'size' => Select2::SIZE_SMALL,
        'pluginOptions' => [
            'closeOnSelect' => false,
            'allowClear' => true,
        ],
        'options' => [
            'placeholder' => 'Choose files...',
            'id' => 'leadFileList',
            'multiple' => true,
        ],
    ]) ?>
</div>
