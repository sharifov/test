<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLog\search\CallLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cl_id') ?>

    <?= $form->field($model, 'cl_parent_id') ?>

    <?= $form->field($model, 'cl_call_sid') ?>

    <?= $form->field($model, 'cl_type_id') ?>

    <?= $form->field($model, 'cl_category_id') ?>

    <?php // echo $form->field($model, 'cl_is_transfer') ?>

    <?php // echo $form->field($model, 'cl_duration') ?>

    <?php // echo $form->field($model, 'cl_phone_from') ?>

    <?php // echo $form->field($model, 'cl_phone_to') ?>

    <?php // echo $form->field($model, 'cl_phone_list_id') ?>

    <?php // echo $form->field($model, 'cl_user_id') ?>

    <?php // echo $form->field($model, 'cl_department_id') ?>

    <?php // echo $form->field($model, 'cl_project_id') ?>

    <?php // echo $form->field($model, 'cl_call_created_dt') ?>

    <?php // echo $form->field($model, 'cl_call_finished_dt') ?>

    <?php // echo $form->field($model, 'cl_status_id') ?>

    <?php // echo $form->field($model, 'cl_client_id') ?>

    <?php // echo $form->field($model, 'cl_price') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
