<?php

/**
 * @var $form ActiveForm
 * @var $split ProfitSplit
 * @var $key string|integer
 * @var $totalProfit float
 * @var $userList []
 * @var $ownerSplit boolean
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$js = <<<JS

var leadId = $leadId || null;

function checkPercentageOfSplit () {
    var form = document.getElementById('split-form');
    var formData = new FormData(form);
    formData.append('leadId', leadId);
    
    $.ajax({
        type: 'post',
        url: '/lead/check-percentage-of-split-validation',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#save-btn').attr('disabled', true);    
        },
        success: function (data) {
            var html = '';
            for (var key in data) {
                html += data[key];
            }
            $('#split-form-notification').html(html);
            $('#save-btn').removeAttr('disabled');
        },
        error: function () {
            $('#save-btn').removeAttr('disabled');    
        }
    })
}

function delay(callback, ms) {
      var timer = 0;
      return function() {
            var context = this, args = arguments;
           
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
      };
}

function calcProfitByPercent(obj, total){
    var preValue = obj.defaultValue;
    var percent = parseInt($(obj).val());
    var diffValue = preValue - percent;
    var amount = (total * percent / 100).toFixed(2);
    $(obj).parents('.split-row').find('.profit-amount').html(amount);

    var otherPercent = 0;
    $('.profit-percent', '#profit-splits').not('#profitsplit-__id__-ps_percent').each(function() {
      otherPercent = otherPercent + parseInt($(this).val());
    })

    mainAgentPercentVal = 100 - otherPercent;
    mainAgentProfitVal = (total * mainAgentPercentVal / 100).toFixed(2);
    
    mainAgentProfit();
}
JS;
$this->registerJs($js);
?>
<div class="row split-row"
    <?php if ($ownerSplit) : ?>
        style="background-color: antiquewhite; padding-top: 10px; margin-bottom: 10px;"
    <?php endif; ?>
>
    <div class="col-md-4">
        <?= $form->field($split, '[' . $key . ']ps_user_id', [
            'template' => '{input}{error}{hint}'
        ])->dropDownList(
            $userList,
            [
                'class' => 'form-control',
                'placeholder' => 'Percent',
            ]
        );?>
    </div>
    <div class="col-md-4">
    <?= $form->field($split, '[' . $key . ']ps_percent', [
        'template' => '{input}{error}{hint}'
    ])->input('number', [
        'min' => 0,
        'max' => 100,
        'class' => "form-control" . ($ownerSplit ? " owner-percent" : " profit-percent"),
        'placeholder' => 'Percent',
        'onchange' => "calcProfitByPercent(this, $totalProfit);",
        'onkeydown' => "delay(checkPercentageOfSplit, 500)();"
    ]);?>
    </div>
    <div class="col-md-1">
        <div class="profit-amount
            <?php if ($ownerSplit) : ?>
                owner-amound
            <?php endif;?>"
             style="display: flex; width: 70px; border: 1px solid #e4e9ee;
              justify-content: center; align-items: center; height: 30px;">
            <?= (!empty($split->ps_percent)) ? number_format($totalProfit * $split->ps_percent / 100, 2, '.', '') : '0' ?>
        </div>
    </div>
    <div class="col-md-3">
        <?php if (!$ownerSplit) : ?>
            <?= Html::button('<i class="fa fa-trash"></i>', [
                'class' => 'btn btn-danger pull-right remove-split-button' ,
            ]); ?>
            <?= $form->field($split, '[' . $key . ']ps_id', [
                'options' => [
                    'tag' => false
                ],
            ])->hiddenInput()->label(false);?>
        <?php endif;?>
    </div>
</div>
