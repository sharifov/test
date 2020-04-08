<?php

use common\models\UserBonusRules;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\UserBonusRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-bonus-rules-form">

    <div class="row">
        <div class="col-md-2">
            <?php Pjax::begin() ?>

                <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'ubr_exp_month')->input('number', [
                    'max' => UserBonusRules::EXP_MAX_VALUE,
                    'min' => UserBonusRules::EXP_MIN_VALUE,
                    'step' => 1
                ]) ?>

                <?= $form->field($model, 'ubr_kpi_percent')->input('number', [
                    'max' => UserBonusRules::VALUE_MAX,
                    'min' => UserBonusRules::VALUE_MIN,
                    'step' => 0.01
                ]) ?>

                <?= $form->field($model, 'ubr_order_profit')->input('number', [
					'min' => UserBonusRules::EXP_MIN_VALUE,
					'step' => 1
                ]) ?>

                <?= $form->field($model, 'ubr_value')->input('number', [
                    'min' => UserBonusRules::EXP_MIN_VALUE,
                    'step' => 1
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            <?php Pjax::end() ?>
        </div>
    </div>

</div>
