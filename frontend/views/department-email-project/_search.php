<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\DepartmentEmailProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-email-project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'dep_id') ?>

    <?= $form->field($model, 'dep_email') ?>

    <?= $form->field($model, 'dep_project_id') ?>

    <?= $form->field($model, 'dep_dep_id') ?>

    <?= $form->field($model, 'dep_source_id') ?>

    <?php // echo $form->field($model, 'dep_enable') ?>

    <?php // echo $form->field($model, 'dep_updated_user_id') ?>

    <?php // echo $form->field($model, 'dep_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
