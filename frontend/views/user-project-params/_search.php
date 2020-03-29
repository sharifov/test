<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserProjectParamsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-project-params-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'upp_user_id') ?>

    <?= $form->field($model, 'upp_project_id') ?>

    <?php //= $form->field($model, 'upp_email') ?>

    <?php //= $form->field($model, 'upp_tw_phone_number') ?>

    <?php // echo $form->field($model, 'upp_tw_sip_id') ?>

    <?php // echo $form->field($model, 'upp_created_dt') ?>

    <?php // echo $form->field($model, 'upp_updated_dt') ?>

    <?php // echo $form->field($model, 'upp_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
