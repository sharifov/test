<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ot_uuid') ?>

    <?= $form->field($model, 'ot_q_id') ?>

    <?= $form->field($model, 'ot_object') ?>

    <?= $form->field($model, 'ot_object_id') ?>

    <?= $form->field($model, 'execution_dt') ?>

    <?php // echo $form->field($model, 'ot_command') ?>

    <?php // echo $form->field($model, 'ot_status') ?>

    <?php // echo $form->field($model, 'ot_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
