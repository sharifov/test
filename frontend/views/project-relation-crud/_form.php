<?php

use common\models\Project;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectRelation\ProjectRelation */
/* @var $form ActiveForm */
?>

<div class="project-relation-form">
    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'prl_project_id')->dropDownList(Project::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'prl_related_project_id')->dropDownList(Project::getList(), ['prompt' => '-']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
