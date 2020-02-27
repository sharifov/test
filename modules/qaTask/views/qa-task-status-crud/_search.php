<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatus\search\QaTaskStatusCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-status-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ts_id') ?>

    <?= $form->field($model, 'ts_name') ?>

    <?= $form->field($model, 'ts_description') ?>

    <?= $form->field($model, 'ts_enabled') ?>

    <?= $form->field($model, 'ts_css_class') ?>

    <?php // echo $form->field($model, 'ts_created_user_id') ?>

    <?php // echo $form->field($model, 'ts_updated_user_id') ?>

    <?php // echo $form->field($model, 'ts_created_dt') ?>

    <?php // echo $form->field($model, 'ts_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
