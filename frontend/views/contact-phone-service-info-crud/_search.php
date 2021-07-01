<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfoSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="contact-phone-service-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cpsi_cpl_id') ?>

    <?= $form->field($model, 'cpsi_service_id') ?>

    <?= $form->field($model, 'cpsi_data_json') ?>

    <?= $form->field($model, 'cpsi_created_dt') ?>

    <?= $form->field($model, 'cpsi_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
