<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponse\entity\search\ClientChatCannedResponseSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-canned-response-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cr_id') ?>

    <?= $form->field($model, 'cr_project_id') ?>

    <?= $form->field($model, 'cr_category_id') ?>

    <?= $form->field($model, 'cr_language_id') ?>

    <?= $form->field($model, 'cr_user_id') ?>

    <?php // echo $form->field($model, 'cr_sort_order') ?>

    <?php // echo $form->field($model, 'cr_message') ?>

    <?php // echo $form->field($model, 'cr_created_dt') ?>

    <?php // echo $form->field($model, 'cr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
