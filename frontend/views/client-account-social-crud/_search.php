<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccountSocial\entity\ClientAccountSocialSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-account-social-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cas_ca_id') ?>

    <?= $form->field($model, 'cas_type_id') ?>

    <?= $form->field($model, 'cas_identity') ?>

    <?= $form->field($model, 'cas_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
