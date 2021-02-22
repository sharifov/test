<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCar\RentCarSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="rent-car-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'prc_id') ?>

    <?= $form->field($model, 'prc_product_id') ?>

    <?= $form->field($model, 'prc_pick_up_code') ?>

    <?= $form->field($model, 'prc_drop_off_code') ?>

    <?= $form->field($model, 'prc_pick_up_date') ?>

    <?php // echo $form->field($model, 'prc_drop_off_date') ?>

    <?php // echo $form->field($model, 'prc_pick_up_time') ?>

    <?php // echo $form->field($model, 'prc_drop_off_time') ?>

    <?php // echo $form->field($model, 'prc_created_dt') ?>

    <?php // echo $form->field($model, 'prc_updated_dt') ?>

    <?php // echo $form->field($model, 'prc_created_user_id') ?>

    <?php // echo $form->field($model, 'prc_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
