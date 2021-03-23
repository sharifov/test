<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productHolder\search\ProductHolderSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="product-holder-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ph_id') ?>

    <?= $form->field($model, 'ph_product_id') ?>

    <?= $form->field($model, 'ph_first_name') ?>

    <?= $form->field($model, 'ph_last_name') ?>

    <?= $form->field($model, 'ph_email') ?>

    <?php // echo $form->field($model, 'ph_phone_number') ?>

    <?php // echo $form->field($model, 'ph_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
