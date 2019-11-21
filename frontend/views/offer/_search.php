<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\OfferSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'of_id') ?>

    <?= $form->field($model, 'of_gid') ?>

    <?= $form->field($model, 'of_uid') ?>

    <?= $form->field($model, 'of_name') ?>

    <?= $form->field($model, 'of_lead_id') ?>

    <?php // echo $form->field($model, 'of_status_id') ?>

    <?php // echo $form->field($model, 'of_owner_user_id') ?>

    <?php // echo $form->field($model, 'of_created_user_id') ?>

    <?php // echo $form->field($model, 'of_updated_user_id') ?>

    <?php // echo $form->field($model, 'of_created_dt') ?>

    <?php // echo $form->field($model, 'of_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
