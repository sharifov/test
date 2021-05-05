<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderContact\search\OrderContactSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="order-contact-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'oc_id') ?>

    <?= $form->field($model, 'oc_order_id') ?>

    <?= $form->field($model, 'oc_first_name') ?>

    <?= $form->field($model, 'oc_last_name') ?>

    <?= $form->field($model, 'oc_middle_name') ?>

    <?php // echo $form->field($model, 'oc_email') ?>

    <?php // echo $form->field($model, 'oc_phone_number') ?>

    <?php // echo $form->field($model, 'oc_created_dt') ?>

    <?php // echo $form->field($model, 'oc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
