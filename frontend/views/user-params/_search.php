<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserParamsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-params-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'up_user_id') ?>

    <?= $form->field($model, 'up_commission_percent') ?>

    <?= $form->field($model, 'up_base_amount') ?>

    <?= $form->field($model, 'up_updated_dt') ?>

    <?= $form->field($model, 'up_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
