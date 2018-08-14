<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ApiUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'au_id') ?>

    <?= $form->field($model, 'au_name') ?>

    <?= $form->field($model, 'au_api_username') ?>

    <?= $form->field($model, 'au_api_password') ?>

    <?= $form->field($model, 'au_email') ?>

    <?php // echo $form->field($model, 'au_project_id') ?>

    <?php // echo $form->field($model, 'au_enabled') ?>

    <?php // echo $form->field($model, 'au_updated_dt') ?>

    <?php // echo $form->field($model, 'au_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
