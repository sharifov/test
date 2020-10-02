<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\actionReason\search\actionReasonSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-action-reason-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccar_id') ?>

    <?= $form->field($model, 'ccar_action_id') ?>

    <?= $form->field($model, 'ccar_key') ?>

    <?= $form->field($model, 'ccar_name') ?>

    <?= $form->field($model, 'ccar_enabled') ?>

    <?php // echo $form->field($model, 'ccar_comment_required') ?>

    <?php // echo $form->field($model, 'ccar_created_user_id') ?>

    <?php // echo $form->field($model, 'ccar_updated_user_id') ?>

    <?php // echo $form->field($model, 'ccar_created_dt') ?>

    <?php // echo $form->field($model, 'ccar_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
