<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseEventLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-event-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cel_id') ?>

    <?= $form->field($model, 'cel_case_id') ?>

    <?= $form->field($model, 'cel_description') ?>

    <?= $form->field($model, 'cel_data_json') ?>

    <?= $form->field($model, 'cel_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
