<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentEmailProject */

$this->title = 'Update Department Email Project: ' . $model->dep_id;
$this->params['breadcrumbs'][] = ['label' => 'Department Email Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dep_id, 'url' => ['view', 'id' => $model->dep_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="department-email-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
