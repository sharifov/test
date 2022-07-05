<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectSegment\src\entities\search\ObjectSegmentTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-segment-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ostl_osl_id') ?>

    <?= $form->field($model, 'ostl_tl_id') ?>

    <?= $form->field($model, 'ostl_created_dt') ?>

    <?= $form->field($model, 'ostl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
