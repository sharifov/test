<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskStatusLog */

$this->title = 'Update Object Task Status Log: ' . $model->otsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->otsl_id, 'url' => ['view', 'otsl_id' => $model->otsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="object-task-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
