<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentEmailProject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-email-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dep_email')->textInput(['maxlength' => true, 'type' => 'email']) ?>

	<?= $form->field($model, 'dep_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>

	<?= $form->field($model, 'dep_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

	<?= $form->field($model, 'dep_source_id')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\Sources::getList(true),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select market', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ])
	?>

	<?= $form->field($model, 'user_group_list')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\UserGroup::getList(),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select User Groups', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ])
	?>

	<?= $form->field($model, 'dep_description')->textarea() ?>

    <?= $form->field($model, 'dep_enable')->checkbox() ?>

    <?= $form->field($model, 'dep_default')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
