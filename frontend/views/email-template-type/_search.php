<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\EmailTemplateTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'etp_id') ?>

    <?= $form->field($model, 'etp_key') ?>

    <?= $form->field($model, 'etp_name') ?>

    <?= $form->field($model, 'etp_created_user_id') ?>

    <?= $form->field($model, 'etp_updated_user_id') ?>

    <?php // echo $form->field($model, 'etp_created_dt') ?>

    <?php // echo $form->field($model, 'etp_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
