<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\search\ExperimentTargetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="experiment-target-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ext_id') ?>

    <?= $form->field($model, 'ext_target_id') ?>

    <?= $form->field($model, 'ext_target_type_id') ?>

    <?= $form->field($model, 'ext_experiment_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
