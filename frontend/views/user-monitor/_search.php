<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-monitor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'um_id') ?>

    <?= $form->field($model, 'um_user_id') ?>

    <?= $form->field($model, 'um_type_id') ?>

    <?= $form->field($model, 'um_start_dt') ?>

    <?= $form->field($model, 'um_end_dt') ?>

    <?php // echo $form->field($model, 'um_period_sec') ?>

    <?php // echo $form->field($model, 'um_description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
