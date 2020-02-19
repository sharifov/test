<?php

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use sales\access\ListsAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="qa-task-category-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'tc_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tc_object_type_id')->dropDownList(QaTaskObjectType::getList(), ['prompt' => 'Select Object Type']) ?>

        <?= $form->field($model, 'tc_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tc_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tc_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?= $form->field($model, 'tc_default')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
