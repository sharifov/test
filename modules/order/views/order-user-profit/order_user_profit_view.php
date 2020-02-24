<?php

use common\widgets\Alert;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\forms\OrderUserProfitFormComposite;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model OrderUserProfitFormComposite */
/* @var $form ActiveForm */
/* @var $order Order */
?>
<div class="modules-order-src-forms">

	<?php \yii\widgets\Pjax::begin(['id' => 'order_user_profit_pjax', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 2000]) ?>

	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

	<?= $form->errorSummary($model) ?>

	<?=
	$form->field($model, 'orderUserProfits')->widget(\unclead\multipleinput\MultipleInput::class, [
		'columns' => [
			[
				'name' => 'oup_order_id',
				'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_HIDDEN_INPUT,
				'defaultValue' => $order->or_id,
			],
			[
				'name' => 'oup_user_id',
				'title' => 'Agent',
				'type'  => 'dropDownList',
				'items' => \common\models\Employee::getList(),
				'options' => [
					'prompt' => '---'
				]
			],
			[
				'name' => 'oup_percent',
				'title' => 'Percent',
				'options' => [
					'type' => 'number',
                    'min' => 0,
                    'max' => 100
				]
			],
			[
				'name' => 'oup_amount',
				'title' => 'Profit Amount',
				'defaultValue' => $order->or_profit_amount ?? 0,
				'options' => [
					'readonly' => true,
					'disabled' => true,
				]
			]
		]
	])->label(false);

	?>

	<?= Html::hiddenInput('orderId', $order->or_id) ?>

	<?= Alert::widget() ?>

    <div class="form-group">
		<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>

	<?php \yii\widgets\Pjax::end() ?>

</div><!-- modules-order-src-forms -->
