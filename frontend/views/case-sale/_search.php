<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CaseSaleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-sale-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'css_cs_id') ?>

    <?= $form->field($model, 'css_sale_id') ?>

    <?= $form->field($model, 'css_sale_book_id') ?>

    <?= $form->field($model, 'css_sale_pnr') ?>

    <?= $form->field($model, 'css_sale_pax') ?>

    <?php // echo $form->field($model, 'css_sale_created_dt') ?>

    <?php // echo $form->field($model, 'css_sale_data') ?>

    <?php // echo $form->field($model, 'css_created_user_id') ?>

    <?php // echo $form->field($model, 'css_updated_user_id') ?>

    <?php // echo $form->field($model, 'css_created_dt') ?>

    <?php // echo $form->field($model, 'css_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
