<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerSendLog\search\OfferSendLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-send-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ofsndl_id') ?>

    <?= $form->field($model, 'ofsndl_offer_id') ?>

    <?= $form->field($model, 'ofsndl_type_id') ?>

    <?= $form->field($model, 'ofsndl_created_dt') ?>

    <?= $form->field($model, 'ofsndl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
