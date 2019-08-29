<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CallUserAccessSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-user-access-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cua_call_id') ?>

    <?= $form->field($model, 'cua_user_id') ?>

    <?= $form->field($model, 'cua_status_id') ?>

    <?= $form->field($model, 'cua_created_dt') ?>

    <?= $form->field($model, 'cua_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
