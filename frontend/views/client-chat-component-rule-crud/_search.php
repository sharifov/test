<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentRule\entity\search\ClientChatComponentRuleSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-component-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cccr_component_event_id') ?>

    <?= $form->field($model, 'cccr_value') ?>

    <?= $form->field($model, 'cccr_runnable_component') ?>

    <?= $form->field($model, 'cccr_component_config') ?>

    <?= $form->field($model, 'cccr_sort_order') ?>

    <?php // echo $form->field($model, 'cccr_enabled') ?>

    <?php // echo $form->field($model, 'cccr_created_user_id') ?>

    <?php // echo $form->field($model, 'cccr_updated_user_id') ?>

    <?php // echo $form->field($model, 'cccr_created_dt') ?>

    <?php // echo $form->field($model, 'cccr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
