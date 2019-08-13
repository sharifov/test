<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\DepartmentPhoneProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-phone-project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'dpp_id') ?>

    <?= $form->field($model, 'dpp_phone_number') ?>

    <?= $form->field($model, 'dpp_project_id') ?>

    <?= $form->field($model, 'dpp_dep_id') ?>

    <?= $form->field($model, 'dpp_source_id') ?>

    <?php // echo $form->field($model, 'dpp_params') ?>

    <?php // echo $form->field($model, 'dpp_ivr_enable') ?>

    <?php // echo $form->field($model, 'dpp_enable') ?>

    <?php // echo $form->field($model, 'dpp_updated_user_id') ?>

    <?php // echo $form->field($model, 'dpp_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
