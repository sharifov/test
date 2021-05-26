<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDocSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="abac-doc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ad_id') ?>

    <?= $form->field($model, 'ad_file') ?>

    <?= $form->field($model, 'ad_line') ?>

    <?= $form->field($model, 'ad_subject') ?>

    <?= $form->field($model, 'ad_object') ?>

    <?php // echo $form->field($model, 'ad_action') ?>

    <?php // echo $form->field($model, 'ad_description') ?>

    <?php // echo $form->field($model, 'ad_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
