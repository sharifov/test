<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\search\CallCommandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-command-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccom_id') ?>

    <?= $form->field($model, 'ccom_parent_id') ?>

    <?= $form->field($model, 'ccom_project_id') ?>

    <?= $form->field($model, 'ccom_lang_id') ?>

    <?= $form->field($model, 'ccom_name') ?>

    <?php // echo $form->field($model, 'ccom_type_id') ?>

    <?php // echo $form->field($model, 'ccom_params_json') ?>

    <?php // echo $form->field($model, 'ccom_sort_order') ?>

    <?php // echo $form->field($model, 'ccom_user_id') ?>

    <?php // echo $form->field($model, 'ccom_created_user_id') ?>

    <?php // echo $form->field($model, 'ccom_updated_user_id') ?>

    <?php // echo $form->field($model, 'ccom_created_dt') ?>

    <?php // echo $form->field($model, 'ccom_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
