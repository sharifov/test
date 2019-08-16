<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CaseNoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-note-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cn_id') ?>

    <?= $form->field($model, 'cn_cs_id') ?>

    <?= $form->field($model, 'cn_user_id') ?>

    <?= $form->field($model, 'cn_text') ?>

    <?= $form->field($model, 'cn_created_dt') ?>

    <?php // echo $form->field($model, 'cn_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
