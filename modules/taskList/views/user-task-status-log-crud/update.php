<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskStatusLog */

$this->title = 'Update User Task Status Log: ' . $model->utsl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->utsl_id, 'url' => ['view', 'utsl_id' => $model->utsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-task-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
