<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadChecklistTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-checklist-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lct_id') ?>

    <?= $form->field($model, 'lct_key') ?>

    <?= $form->field($model, 'lct_name') ?>

    <?= $form->field($model, 'lct_description') ?>

    <?= $form->field($model, 'lct_enabled') ?>

    <?php // echo $form->field($model, 'lct_sort_order') ?>

    <?php // echo $form->field($model, 'lct_updated_dt') ?>

    <?php // echo $form->field($model, 'lct_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
