<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userActivity\entity\search\UserActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-activity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ua_user_id') ?>

    <?= $form->field($model, 'ua_object_event') ?>

    <?= $form->field($model, 'ua_object_id') ?>

    <?= $form->field($model, 'ua_start_dt') ?>

    <?= $form->field($model, 'ua_end_dt') ?>

    <?php // echo $form->field($model, 'ua_type_id') ?>

    <?php // echo $form->field($model, 'ua_description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
