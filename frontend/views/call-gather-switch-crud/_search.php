<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\search\CallGatherSwitchSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-gather-switch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cgs_ccom_id') ?>

    <?= $form->field($model, 'cgs_step') ?>

    <?= $form->field($model, 'cgs_case') ?>

    <?= $form->field($model, 'cgs_exec_ccom_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
