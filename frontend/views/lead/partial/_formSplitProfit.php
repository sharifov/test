<?php
/**
 * @var $form ActiveForm
 * @var $split ProfitSplit
 * @var $key string|integer
 * @var $totalProfit float
 * @var $userList []
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
    var amount;
    var percent = parseInt($(obj).val());
    amount = (+(total * percent / 100).toFixed(4));
    $(obj).parents('.split-row').find('.profit-amount').html(amount);
    mainAgentProfit();
}
JS;
$this->registerJs($js);
?>
<div class="row split-row">
	<div class="col-md-4">
    	<?= $form->field($split, '[' . $key . ']ps_user_id', [
            'template' => '{input}{error}{hint}'
        ])->dropDownList(
            $userList,
            [
            'class' => 'form-control',
            'placeholder' => 'Percent',
        ]);?>
	</div>
	<div class="col-md-4">
	<?= $form->field($split, '[' . $key . ']ps_percent', [
        'template' => '{input}{error}{hint}'
    ])->input('number',[
        'min' => 0,
        'max' => 100,
        'class' => 'form-control',
        'placeholder' => 'Percent',
        'onchange' => "calcProfitByPercent(this, $totalProfit);",
        'onkeydown' => "delay(checkPercentageOfSplit, 500)();"
    ]);?>
	</div>
	<div class="col-md-1">
        <div class="profit-amount" style="display: flex; width: 70px; border: 1px solid #e4e9ee; justify-content: center; align-items: center; height: 30px;">
			<?= (!empty($split->ps_percent))?$totalProfit*$split->ps_percent/100:'0'?>
        </div>
	</div>
	<div class="col-md-3">
		<?= Html::button('<i class="fa fa-trash"></i>', [
            'class' => 'btn btn-danger pull-right remove-split-button' ,
        ]); ?>
        <?= $form->field($split, '[' . $key . ']ps_id', [
            'options' => [
                'tag' => false
            ],
        ])->hiddenInput()->label(false);
        ?>
	</div>
</div>