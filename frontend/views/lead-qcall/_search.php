<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadQcallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-qcall-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lqc_lead_id') ?>

    <?= $form->field($model, 'lqc_dt_from') ?>

    <?= $form->field($model, 'lqc_dt_to') ?>

    <?= $form->field($model, 'lqc_weight') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
