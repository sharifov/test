<?php

use common\models\LeadProfitType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadProfitType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-profit-type-form">

    <div class="row">
        <div class="col-md-2">
            <?php \yii\widgets\Pjax::begin([]) ?>

			<?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'lpt_profit_type_id')->dropDownList(LeadProfitType::getProfitTypeList(), ['prompt' => '---']) ?>

                <?= $form->field($model, 'lpt_diff_rule')->input('number', [
                    'min' => LeadProfitType::MIN_PERCENT_VALUE,
                    'max' => LeadProfitType::MAX_PERCENT_VALUE
                ]) ?>

                <?= $form->field($model, 'lpt_commission_min')->input('number', [
                        'min' => LeadProfitType::MIN_PERCENT_VALUE,
                        'max' => LeadProfitType::MAX_PERCENT_VALUE
                ]) ?>

                <?= $form->field($model, 'lpt_commission_max')->input('number', [
					'min' => LeadProfitType::MIN_PERCENT_VALUE,
					'max' => LeadProfitType::MAX_PERCENT_VALUE
				]) ?>

                <?= $form->field($model, 'lpt_commission_fix')->input('number', [
					'min' => LeadProfitType::MIN_PERCENT_VALUE,
					'max' => LeadProfitType::MAX_PERCENT_VALUE
				]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>

			<?php ActiveForm::end(); ?>

            <?php \yii\widgets\Pjax::end() ?>
        </div>
    </div>


</div>
