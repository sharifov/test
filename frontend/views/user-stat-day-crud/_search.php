<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userStatDay\entity\search\UserStatDaySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-stat-day-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'usd_id') ?>

    <?= $form->field($model, 'usd_key') ?>

    <?= $form->field($model, 'usd_value') ?>

    <?= $form->field($model, 'usd_user_id') ?>

    <?= $form->field($model, 'usd_day') ?>

    <?php // echo $form->field($model, 'usd_month') ?>

    <?php // echo $form->field($model, 'usd_year') ?>

    <?php // echo $form->field($model, 'usd_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
