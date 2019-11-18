<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ProductTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pt_id') ?>

    <?= $form->field($model, 'pt_key') ?>

    <?= $form->field($model, 'pt_name') ?>

    <?= $form->field($model, 'pt_description') ?>

    <?= $form->field($model, 'pt_settings') ?>

    <?php // echo $form->field($model, 'pt_enabled') ?>

    <?php // echo $form->field($model, 'pt_created_dt') ?>

    <?php // echo $form->field($model, 'pt_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
