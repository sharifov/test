<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectRelation\ProjectRelation */

$this->title = 'Create Project Relation';
$this->params['breadcrumbs'][] = ['label' => 'Project Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
