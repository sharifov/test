<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userStatus\search\UserStatusSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-status-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'us_user_id') ?>

    <?= $form->field($model, 'us_gl_call_count') ?>

    <?= $form->field($model, 'us_call_phone_status') ?>

    <?= $form->field($model, 'us_is_on_call') ?>

    <?= $form->field($model, 'us_has_call_access') ?>

    <?php // echo $form->field($model, 'us_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
