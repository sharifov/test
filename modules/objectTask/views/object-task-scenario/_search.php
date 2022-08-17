<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskScenarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-task-scenario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ots_id') ?>

    <?= $form->field($model, 'ots_key') ?>

    <?= $form->field($model, 'ots_data_json') ?>

    <?= $form->field($model, 'ots_updated_dt') ?>

    <?= $form->field($model, 'ots_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
