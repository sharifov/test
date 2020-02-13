<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerViewLog\search\OfferViewLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-view-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ofvwl_id') ?>

    <?= $form->field($model, 'ofvwl_offer_id') ?>

    <?= $form->field($model, 'ofvwl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
