<?php

use common\models\Project;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectWeight */

$this->title = 'Create Project Weight';
$this->params['breadcrumbs'][] = ['label' => 'Project Weights', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-weight-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="project-weight-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pw_project_id')->dropDownList(Project::getList(), ['prompt' => 'Select project']) ?>

        <?= $form->field($model, 'pw_weight')->textInput(['type' => 'number']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
