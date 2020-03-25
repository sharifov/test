<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogQueue\search\CallLogQueueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-queue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'clq_cl_id') ?>

    <?= $form->field($model, 'clq_queue_time') ?>

    <?= $form->field($model, 'clq_access_count') ?>

    <?= $form->field($model, 'clq_is_transfer') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
