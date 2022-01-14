<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\flightQuoteLabelList\entity\FlightQuoteLabelListSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-label-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqll_id') ?>

    <?= $form->field($model, 'fqll_label_key') ?>

    <?= $form->field($model, 'fqll_origin_description') ?>

    <?= $form->field($model, 'fqll_description') ?>

    <?= $form->field($model, 'fqll_created_dt') ?>

    <?php // echo $form->field($model, 'fqll_updated_dt') ?>

    <?php // echo $form->field($model, 'fqll_created_user_id') ?>

    <?php // echo $form->field($model, 'fqll_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
