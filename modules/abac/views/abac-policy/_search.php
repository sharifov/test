<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\search\AbacPolicySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="abac-policy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ap_id') ?>

    <?= $form->field($model, 'ap_rule_type') ?>

    <?= $form->field($model, 'ap_subject') ?>

    <?= $form->field($model, 'ap_subject_json') ?>

    <?= $form->field($model, 'ap_object') ?>

    <?php // echo $form->field($model, 'ap_action') ?>

    <?php // echo $form->field($model, 'ap_action_json') ?>

    <?php // echo $form->field($model, 'ap_effect') ?>

    <?php // echo $form->field($model, 'ap_title') ?>

    <?php // echo $form->field($model, 'ap_sort_order') ?>

    <?php // echo $form->field($model, 'ap_created_dt') ?>

    <?php // echo $form->field($model, 'ap_updated_dt') ?>

    <?php // echo $form->field($model, 'ap_created_user_id') ?>

    <?php // echo $form->field($model, 'ap_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
