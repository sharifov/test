<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadChecklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-checklist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lc_type_id') ?>

    <?= $form->field($model, 'lc_lead_id') ?>

    <?= $form->field($model, 'lc_user_id') ?>

    <?= $form->field($model, 'lc_notes') ?>

    <?= $form->field($model, 'lc_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
