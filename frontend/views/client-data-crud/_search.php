<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientData\entity\ClientDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cd_id') ?>

    <?= $form->field($model, 'cd_client_id') ?>

    <?= $form->field($model, 'cd_key_id') ?>

    <?= $form->field($model, 'cd_field_value') ?>

    <?= $form->field($model, 'cd_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
