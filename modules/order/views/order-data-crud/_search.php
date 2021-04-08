<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderData\search\OrderDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="order-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'od_id') ?>

    <?= $form->field($model, 'od_order_id') ?>

    <?= $form->field($model, 'od_display_uid') ?>

    <?= $form->field($model, 'od_source_id') ?>

    <?= $form->field($model, 'od_created_by') ?>

    <?php // echo $form->field($model, 'od_updated_by') ?>

    <?php // echo $form->field($model, 'od_created_dt') ?>

    <?php // echo $form->field($model, 'od_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
