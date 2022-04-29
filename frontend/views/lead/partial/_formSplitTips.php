<?php

/**
 * @var $form ActiveForm
 * @var $split TipsSplit
 * @var $key string|integer
 * @var $totalTips float
 * @var $changeSplitTips bool
 * @var $userList []
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$js = <<<JS
function calcTipsByPercent(obj, total){
    var amount;
    var percent = parseInt($(obj).val());
    amount = total * percent / 100;
    $(obj).parents('.split-row').find('.tips-amount').val(amount);
    mainAgentTips();
}
JS;
$this->registerJs($js);
?>
<div class="row split-row">
    <div class="col-md-4">
        <?= $form->field($split, '[' . $key . ']ts_user_id', [
            'template' => '{input}{error}{hint}'
        ])->dropDownList(
            $userList,
            [
            'class' => 'form-control',
            'placeholder' => 'Percent',
                'disabled' => !$changeSplitTips
            ]
        );?>
    </div>
    <div class="col-md-4">
    <?= $form->field($split, '[' . $key . ']ts_percent', [
        'template' => '{input}{error}{hint}'
    ])->input('number', [
        'min' => 0,
        'max' => 100,
        'class' => 'form-control',
        'placeholder' => 'Percent',
        'onchange' => "calcTipsByPercent(this, $totalTips);",
        'readonly' => !$changeSplitTips
    ]);?>
    </div>
    <div class="col-md-3">
        <input type="text" class="tips-amount form-control" readonly value="<?= (!empty($split->ts_percent)) ? $totalTips * $split->ts_percent / 100 : '0'?>"/>
    </div>
    <div class="col-md-1">
        <?php
        if ($changeSplitTips) :?>
            <?= Html::button('<i class="fa fa-trash"></i>', [
                'class' => 'btn btn-danger pull-right remove-split-button' ,
            ]); ?>
            <?= $form->field($split, '[' . $key . ']ts_id', [
                'options' => [
                    'tag' => false
                ],
            ])->hiddenInput()->label(false);
            ?>
        <?php endif;?>
    </div>
</div>