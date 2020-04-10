<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadProfitTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-profit-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lpt_profit_type_id') ?>

    <?= $form->field($model, 'lpt_diff_rule') ?>

    <?= $form->field($model, 'lpt_commission_min') ?>

    <?= $form->field($model, 'lpt_commission_max') ?>

    <?= $form->field($model, 'lpt_commission_fix') ?>

    <?php // echo $form->field($model, 'lpt_created_user_id') ?>

    <?php // echo $form->field($model, 'lpt_updated_user_id') ?>

    <?php // echo $form->field($model, 'lpt_created_dt') ?>

    <?php // echo $form->field($model, 'lpt_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
