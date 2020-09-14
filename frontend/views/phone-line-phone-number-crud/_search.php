<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLinePhoneNumber\entity\search\PhoneLinePhoneNumberSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-line-phone-number-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'plpn_line_id') ?>

    <?= $form->field($model, 'plpn_pl_id') ?>

    <?= $form->field($model, 'plpn_default') ?>

    <?= $form->field($model, 'plpn_enabled') ?>

    <?= $form->field($model, 'plpn_settings_json') ?>

    <?php // echo $form->field($model, 'plpn_created_user_id') ?>

    <?php // echo $form->field($model, 'plpn_updated_user_id') ?>

    <?php // echo $form->field($model, 'plpn_created_dt') ?>

    <?php // echo $form->field($model, 'plpn_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
