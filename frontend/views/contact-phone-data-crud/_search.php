<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneData\entity\ContactPhoneDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="contact-phone-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cpd_cpl_id') ?>

    <?= $form->field($model, 'cpd_key') ?>

    <?= $form->field($model, 'cpd_value') ?>

    <?= $form->field($model, 'cpd_created_dt') ?>

    <?= $form->field($model, 'cpd_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
