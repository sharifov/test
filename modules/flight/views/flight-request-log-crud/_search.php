<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightRequestLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-request-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'flr_id') ?>

    <?= $form->field($model, 'flr_fr_id') ?>

    <?= $form->field($model, 'flr_status_id_old') ?>

    <?= $form->field($model, 'flr_status_id_new') ?>

    <?= $form->field($model, 'flr_description') ?>

    <?php // echo $form->field($model, 'flr_created_dt') ?>

    <?php // echo $form->field($model, 'flr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
