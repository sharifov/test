<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'lbeqr_id') ?>

    <?= $form->field($model, 'lbeqr_key') ?>

    <?= $form->field($model, 'lbeqr_name') ?>

    <?= $form->field($model, 'lbeqr_description') ?>

    <?= $form->field($model, 'lbeqr_params_json') ?>

    <?php // echo $form->field($model, 'lbeqr_updated_user_id') ?>

    <?php // echo $form->field($model, 'lbeqr_created_dt') ?>

    <?php // echo $form->field($model, 'lbeqr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
