<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lt_lead_id') ?>

    <?= $form->field($model, 'lt_task_id') ?>

    <?= $form->field($model, 'lt_user_id') ?>

    <?= $form->field($model, 'lt_date') ?>

    <?= $form->field($model, 'lt_notes') ?>

    <?php // echo $form->field($model, 'lt_completed_dt') ?>

    <?php // echo $form->field($model, 'lt_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
