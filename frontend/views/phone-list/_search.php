<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneList\entity\search\PhoneListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pl_id') ?>

    <?= $form->field($model, 'pl_phone_number') ?>

    <?= $form->field($model, 'pl_title') ?>

    <?= $form->field($model, 'pl_enabled') ?>

    <?= $form->field($model, 'pl_created_user_id') ?>

    <?php // echo $form->field($model, 'pl_updated_user_id') ?>

    <?php // echo $form->field($model, 'pl_created_dt') ?>

    <?php // echo $form->field($model, 'pl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
