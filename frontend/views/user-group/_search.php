<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserGroupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ug_id') ?>

    <?= $form->field($model, 'ug_key') ?>

    <?= $form->field($model, 'ug_name') ?>

    <?= $form->field($model, 'ug_description') ?>

    <?= $form->field($model, 'ug_disable') ?>

    <?php // echo $form->field($model, 'ug_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
