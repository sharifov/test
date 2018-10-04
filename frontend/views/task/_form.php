<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 't_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 't_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 't_category_id')->dropDownList(\common\models\Task::CAT_LIST, ['prompt' => '-']) ?>

        <?= $form->field($model, 't_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 't_hidden')->checkbox() ?>

        <?= $form->field($model, 't_sort_order')->input('number') ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
