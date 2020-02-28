<?php

use common\widgets\Alert;
use modules\order\src\entities\order\Order;
use modules\order\src\forms\OrderTipsUserProfitFormComposite;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model OrderTipsUserProfitFormComposite */
/* @var $form ActiveForm */
/* @var $order Order */
?>
<div class="modules-order-src-forms">

	<?php \yii\widgets\Pjax::begin(['id' => 'order_tips_user_profit_pjax', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 2000]) ?>

	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

	<?= $form->errorSummary($model) ?>

	<?=
	$form->field($model, 'orderTipsUserProfits')->widget(\unclead\multipleinput\MultipleInput::class, [
		'columns' => [
			[
				'name' => 'otup_order_id',
				'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_HIDDEN_INPUT,
				'defaultValue' => $order->or_id,
			],
			[
				'name' => 'otup_user_id',
				'title' => 'Agent',
				'type'  => 'dropDownList',
				'items' => \common\models\Employee::getList(),
				'options' => [
					'prompt' => '---'
				]
			],
			[
				'name' => 'otup_percent',
				'title' => 'Percent',
				'options' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100
				]
			],
			[
				'name' => 'otup_amount',
				'title' => 'Profit Amount',
				'defaultValue' => $order->orderTips->ot_user_profit ?? 0,
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
