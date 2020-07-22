<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitorData\entity\search\ClientChatVisitorSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-visitor-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cvd_id') ?>

    <?= $form->field($model, 'cvd_country') ?>

    <?= $form->field($model, 'cvd_region') ?>

    <?= $form->field($model, 'cvd_city') ?>

    <?= $form->field($model, 'cvd_latitude') ?>

    <?php // echo $form->field($model, 'cvd_longitude') ?>

    <?php // echo $form->field($model, 'cvd_url') ?>

    <?php // echo $form->field($model, 'cvd_title') ?>

    <?php // echo $form->field($model, 'cvd_referrer') ?>

    <?php // echo $form->field($model, 'cvd_timezone') ?>

    <?php // echo $form->field($model, 'cvd_local_time') ?>

    <?php // echo $form->field($model, 'cvd_data') ?>

    <?php // echo $form->field($model, 'cvd_created_dt') ?>

    <?php // echo $form->field($model, 'cvd_updated_dt') ?>

    <?php // echo $form->field($model, 'cvd_visitor_rc_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
