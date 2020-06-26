<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailUnsubscribeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-unsubscribe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'eu_email') ?>

    <?= $form->field($model, 'eu_project_id') ?>

    <?= $form->field($model, 'eu_created_user_id') ?>

    <?= $form->field($model, 'eu_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
