<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatForm\entity\ClientChatFormSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-form-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccf_id') ?>

    <?= $form->field($model, 'ccf_key') ?>

    <?= $form->field($model, 'ccf_name') ?>

    <?= $form->field($model, 'ccf_project_id') ?>

    <?= $form->field($model, 'ccf_dataform_json') ?>

    <?php // echo $form->field($model, 'ccf_enabled') ?>

    <?php // echo $form->field($model, 'ccf_created_user_id') ?>

    <?php // echo $form->field($model, 'ccf_updated_user_id') ?>

    <?php // echo $form->field($model, 'ccf_created_dt') ?>

    <?php // echo $form->field($model, 'ccf_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
