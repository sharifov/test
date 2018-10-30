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
function calcProfitByPercent(obj, total){
    var amount;
    var percent = parseInt($(obj).val());
    amount = total * percent / 100;
    $(obj).parents('.split-row').find('.profit-amount').val(amount);
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
    ]);?>
	</div>
	<div class="col-md-3">
		<input type="text" class="profit-amount form-control" readonly value="<?= (!empty($split->ps_percent))?$totalProfit*$split->ps_percent/100:'0'?>"/>
	</div>
	<div class="col-md-1">
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