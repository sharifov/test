<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\search\EventListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'el_id') ?>

    <?= $form->field($model, 'el_key') ?>

    <?= $form->field($model, 'el_category') ?>

    <?= $form->field($model, 'el_description') ?>

    <?= $form->field($model, 'el_enable_type') ?>

    <?php // echo $form->field($model, 'el_enable_log') ?>

    <?php // echo $form->field($model, 'el_break') ?>

    <?php // echo $form->field($model, 'el_sort_order') ?>

    <?php // echo $form->field($model, 'el_cron_expression') ?>

    <?php // echo $form->field($model, 'el_condition') ?>

    <?php // echo $form->field($model, 'el_builder_json') ?>

    <?php // echo $form->field($model, 'el_updated_dt') ?>

    <?php // echo $form->field($model, 'el_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
