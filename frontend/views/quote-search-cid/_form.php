<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSearchCid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-search-cid-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qsc_q_id')->textInput() ?>

    <?= $form->field($model, 'qsc_cid')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
