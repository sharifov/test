<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ProfitBonusSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profit-bonus-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pb_id') ?>

    <?= $form->field($model, 'pb_user_id') ?>

    <?= $form->field($model, 'pb_min_profit') ?>

    <?= $form->field($model, 'pb_bonus') ?>

    <?= $form->field($model, 'pb_updated_dt') ?>

    <?php // echo $form->field($model, 'pb_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
