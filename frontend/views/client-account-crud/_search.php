<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccount\entity\ClientAccountSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ca_id') ?>

    <?= $form->field($model, 'ca_project_id') ?>

    <?= $form->field($model, 'ca_uuid') ?>

    <?= $form->field($model, 'ca_hid') ?>

    <?= $form->field($model, 'ca_username') ?>

    <?php // echo $form->field($model, 'ca_first_name') ?>

    <?php // echo $form->field($model, 'ca_middle_name') ?>

    <?php // echo $form->field($model, 'ca_last_name') ?>

    <?php // echo $form->field($model, 'ca_nationality_country_code') ?>

    <?php // echo $form->field($model, 'ca_dob') ?>

    <?php // echo $form->field($model, 'ca_gender') ?>

    <?php // echo $form->field($model, 'ca_phone') ?>

    <?php // echo $form->field($model, 'ca_subscription') ?>

    <?php // echo $form->field($model, 'ca_language_id') ?>

    <?php // echo $form->field($model, 'ca_currency_code') ?>

    <?php // echo $form->field($model, 'ca_timezone') ?>

    <?php // echo $form->field($model, 'ca_created_ip') ?>

    <?php // echo $form->field($model, 'ca_enabled') ?>

    <?php // echo $form->field($model, 'ca_origin_created_dt') ?>

    <?php // echo $form->field($model, 'ca_origin_updated_dt') ?>

    <?php // echo $form->field($model, 'ca_created_dt') ?>

    <?php // echo $form->field($model, 'ca_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
