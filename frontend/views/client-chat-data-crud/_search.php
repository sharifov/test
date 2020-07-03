<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatData\entity\search\ClientChatDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccd_cch_id') ?>

    <?= $form->field($model, 'ccd_country') ?>

    <?= $form->field($model, 'ccd_region') ?>

    <?= $form->field($model, 'ccd_city') ?>

    <?= $form->field($model, 'ccd_latitude') ?>

    <?php // echo $form->field($model, 'ccd_longitude') ?>

    <?php // echo $form->field($model, 'ccd_url') ?>

    <?php // echo $form->field($model, 'ccd_title') ?>

    <?php // echo $form->field($model, 'ccd_referrer') ?>

    <?php // echo $form->field($model, 'ccd_timezone') ?>

    <?php // echo $form->field($model, 'ccd_local_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
