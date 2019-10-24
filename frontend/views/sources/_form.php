<?php

use common\models\Sources;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sources */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sources-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">

        <?//= $form->field($model, 'project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '---']) ?>

        <?//= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'cid')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'last_update')->textInput() ?>


        <?= $form->field($model, 'default')->checkbox() ?>

        <?= $form->field($model, 'hidden')->checkbox() ?>

        <?= $form->field($model, 'rule')->dropDownList(Sources::LIST_RULES) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
