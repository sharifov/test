<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannel\entity\search\ClientChatChannelSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccc_id') ?>

    <?= $form->field($model, 'ccc_name') ?>

    <?= $form->field($model, 'ccc_project_id') ?>

    <?= $form->field($model, 'ccc_dep_id') ?>

    <?= $form->field($model, 'ccc_ug_id') ?>

    <?php // echo $form->field($model, 'ccc_disabled') ?>

    <?php // echo $form->field($model, 'ccc_created_dt') ?>

    <?php // echo $form->field($model, 'ccc_updated_dt') ?>

    <?php // echo $form->field($model, 'ccc_created_user_id') ?>

    <?php // echo $form->field($model, 'ccc_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
