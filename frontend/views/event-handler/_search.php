<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\search\EventHandlerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-handler-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'eh_id') ?>

    <?= $form->field($model, 'eh_el_id') ?>

    <?= $form->field($model, 'eh_class') ?>

    <?= $form->field($model, 'eh_method') ?>

    <?= $form->field($model, 'eh_enable_type') ?>

    <?php // echo $form->field($model, 'eh_enable_log') ?>

    <?php // echo $form->field($model, 'eh_asynch') ?>

    <?php // echo $form->field($model, 'eh_break') ?>

    <?php // echo $form->field($model, 'eh_sort_order') ?>

    <?php // echo $form->field($model, 'eh_cron_expression') ?>

    <?php // echo $form->field($model, 'eh_condition') ?>

    <?php // echo $form->field($model, 'eh_builder_json') ?>

    <?php // echo $form->field($model, 'eh_updated_dt') ?>

    <?php // echo $form->field($model, 'eh_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
