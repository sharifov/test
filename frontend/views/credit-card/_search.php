<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CreditCardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credit-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cc_id') ?>

    <?= $form->field($model, 'cc_number') ?>

    <?= $form->field($model, 'cc_display_number') ?>

    <?= $form->field($model, 'cc_holder_name') ?>

    <?= $form->field($model, 'cc_expiration_month') ?>

    <?php // echo $form->field($model, 'cc_expiration_year') ?>

    <?php // echo $form->field($model, 'cc_cvv') ?>

    <?php // echo $form->field($model, 'cc_type_id') ?>

    <?php // echo $form->field($model, 'cc_status_id') ?>

    <?php // echo $form->field($model, 'cc_is_expired') ?>

    <?php // echo $form->field($model, 'cc_created_user_id') ?>

    <?php // echo $form->field($model, 'cc_updated_user_id') ?>

    <?php // echo $form->field($model, 'cc_created_dt') ?>

    <?php // echo $form->field($model, 'cc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
