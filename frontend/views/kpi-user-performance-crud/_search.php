<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\search\KpiUserPerformanceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-user-performance-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'up_user_id') ?>

    <?= $form->field($model, 'up_year') ?>

    <?= $form->field($model, 'up_month') ?>

    <?= $form->field($model, 'up_performance') ?>

    <?= $form->field($model, 'up_created_user_id') ?>

    <?php // echo $form->field($model, 'up_updated_user_id') ?>

    <?php // echo $form->field($model, 'up_created_dt') ?>

    <?php // echo $form->field($model, 'up_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
