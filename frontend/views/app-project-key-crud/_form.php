<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\appProjectKey\entity\AppProjectKey */
/* @var $form ActiveForm */
?>

<div class="app-project-key-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'apk_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'apk_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'apk_project_source_id')->dropDownList(\common\models\Sources::getList(), ['prompt' => '-']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
