<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectLocale\ProjectLocale */

$this->title = 'Update Project Locale: ' . $model->pl_project_id;
$this->params['breadcrumbs'][] = ['label' => 'Project Locales', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pl_project_id, 'url' => ['view', 'id' => $model->pl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-locale-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'copyModel' => null,
    ]) ?>

</div>
