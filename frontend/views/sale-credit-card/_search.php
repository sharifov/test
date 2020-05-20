<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SaleCreditCardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-credit-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'scc_sale_id') ?>

    <?= $form->field($model, 'scc_cc_id') ?>

    <?= $form->field($model, 'scc_created_dt') ?>

    <?= $form->field($model, 'scc_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
