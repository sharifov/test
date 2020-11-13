<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\search\PhoneLineCommandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-line-command-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'plc_id') ?>

    <?= $form->field($model, 'plc_line_id') ?>

    <?= $form->field($model, 'plc_ccom_id') ?>

    <?= $form->field($model, 'plc_sort_order') ?>

    <?= $form->field($model, 'plc_created_user_id') ?>

    <?php // echo $form->field($model, 'plc_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
