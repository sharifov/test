<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskCategory\search\QaTaskCategoryCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tc_id') ?>

    <?= $form->field($model, 'tc_key') ?>

    <?= $form->field($model, 'tc_object_type_id') ?>

    <?= $form->field($model, 'tc_name') ?>

    <?= $form->field($model, 'tc_description') ?>

    <?php // echo $form->field($model, 'tc_enabled') ?>

    <?php // echo $form->field($model, 'tc_default') ?>

    <?php // echo $form->field($model, 'tc_created_user_id') ?>

    <?php // echo $form->field($model, 'tc_updated_user_id') ?>

    <?php // echo $form->field($model, 'tc_created_dt') ?>

    <?php // echo $form->field($model, 'tc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
