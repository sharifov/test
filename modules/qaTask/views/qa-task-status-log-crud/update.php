<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model QaTaskStatusLog */

$this->title = 'Update Qa Task Status Log: ' . $model->tsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tsl_id, 'url' => ['view', 'id' => $model->tsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
