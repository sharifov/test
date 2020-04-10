<?php

use sales\model\emailList\entity\search\EmailListSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model EmailListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'el_id') ?>

    <?= $form->field($model, 'el_email') ?>

    <?= $form->field($model, 'el_title') ?>

    <?= $form->field($model, 'el_enabled') ?>

    <?= $form->field($model, 'el_created_user_id') ?>

    <?php // echo $form->field($model, 'el_updated_user_id') ?>

    <?php // echo $form->field($model, 'el_created_dt') ?>

    <?php // echo $form->field($model, 'el_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
