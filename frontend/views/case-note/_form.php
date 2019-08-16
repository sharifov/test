<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CaseNote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cn_cs_id')->textInput() ?>

    <?= $form->field($model, 'cn_user_id')->textInput() ?>

    <?= $form->field($model, 'cn_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cn_created_dt')->textInput() ?>

    <?= $form->field($model, 'cn_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
