<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneList\entity\ContactPhoneListSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="contact-phone-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cpl_id') ?>

    <?= $form->field($model, 'cpl_phone_number') ?>

    <?= $form->field($model, 'cpl_uid') ?>

    <?= $form->field($model, 'cpl_title') ?>

    <?= $form->field($model, 'cpl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
