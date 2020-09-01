<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLine\search\PhoneLineSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-line-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'line_id') ?>

    <?= $form->field($model, 'line_name') ?>

    <?= $form->field($model, 'line_project_id') ?>

    <?= $form->field($model, 'line_dep_id') ?>

    <?= $form->field($model, 'line_language_id') ?>

    <?php // echo $form->field($model, 'line_settings_json') ?>

    <?php // echo $form->field($model, 'line_personal_user_id') ?>

    <?php // echo $form->field($model, 'line_uvm_id') ?>

    <?php // echo $form->field($model, 'line_allow_in') ?>

    <?php // echo $form->field($model, 'line_allow_out') ?>

    <?php // echo $form->field($model, 'line_enabled') ?>

    <?php // echo $form->field($model, 'line_created_user_id') ?>

    <?php // echo $form->field($model, 'line_updated_user_id') ?>

    <?php // echo $form->field($model, 'line_created_dt') ?>

    <?php // echo $form->field($model, 'line_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
