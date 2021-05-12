<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectRelation\ProjectRelation */

$this->title = 'Update Project Relation: ' . $model->prl_project_id;
$this->params['breadcrumbs'][] = ['label' => 'Project Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->prl_project_id, 'url' => ['view', 'prl_project_id' => $model->prl_project_id, 'prl_related_project_id' => $model->prl_related_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-relation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
