<?php

use common\models\UserCommissionRules;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserCommissionRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-commission-rules-form">

    <div class="row">
        <div class="col-md-2">

            <?php \yii\widgets\Pjax::begin() ?>

                <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'ucr_exp_month')->input('number', [
                    'min' => UserCommissionRules::EXP_MIN_VALUE,
                    'max' => UserCommissionRules::EXP_MAX_VALUE,
                    'step' => 1
                ]) ?>

                <?= $form->field($model, 'ucr_kpi_percent')->input('number', [
					'min' => UserCommissionRules::VALUE_MIN,
					'max' => UserCommissionRules::VALUE_MAX,
					'step' => 1
                ]) ?>

                <?= $form->field($model, 'ucr_order_profit')->input('number', [
                    'step' => 1
                ]) ?>

                <?= $form->field($model, 'ucr_value')->input('number', [
					'min' => UserCommissionRules::VALUE_MIN,
					'max' => UserCommissionRules::VALUE_MAX,
					'step' => 0.01
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            <?php \yii\widgets\Pjax::end() ?>
        </div>
    </div>


</div>
