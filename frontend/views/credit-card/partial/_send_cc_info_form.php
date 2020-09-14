<?php

use sales\forms\caseSale\CaseSaleSendCcInfoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var $formCaseSale CaseSaleSendCcInfoForm */
?>

<script>pjaxOffFormSubmit('#case-sale-send-cc-info')</script>
<div class="row">
	<div class="col-md-12">
		<?php Pjax::begin(['id' => 'case-sale-send-cc-info', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000, 'clientOptions' => ['async' => false]]) ?>
			<?php $form = ActiveForm::begin([
				'options' => [
					'data-pjax' => 1
				]
			]) ?>

            <?= $form->errorSummary($formCaseSale) ?>

			<?= $form->field($formCaseSale, 'email')->dropDownList($formCaseSale->emailList) ?>

            <div class="d-flex justify-content-center">
                <?= Html::submitButton('<i class="fa fa-envelope"></i> Send CC Info', ['class' => 'btn btn-success']); ?>
            </div>

			<?php ActiveForm::end(); ?>
		<?php Pjax::end() ?>
	</div>
</div>
