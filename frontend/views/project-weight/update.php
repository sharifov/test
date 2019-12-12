<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectWeight */

$this->title = 'Update Project Weight: ' . $model->project->name;
$this->params['breadcrumbs'][] = ['label' => 'Project Weights', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->project->name, 'url' => ['view', 'id' => $model->pw_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-weight-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="project-weight-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pw_weight')->textInput(['type' => 'number']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
