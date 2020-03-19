<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Department;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cc_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cc_dep_id')->dropDownList(Department::getList(), ['prompt' => 'Select department']) ?>

    <?= $form->field($model, 'cc_system')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
