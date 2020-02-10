<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model QaTaskStatusLog */

$this->title = $model->tsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->tsl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'tsl_id',
            'task:qaTask',
            'tsl_start_status_id:qaTaskStatus',
            'tsl_end_status_id:qaTaskStatus',
            'tsl_start_dt:byUserDateTime',
            'tsl_end_dt:byUserDateTime',
            'tsl_duration:duration',
            'reason.tsr_name',
            'tsl_description',
            'tsl_action_id:qaTaskStatusAction',
            'assignedUser:userName',
            'createdUser:userName',
        ],
    ]) ?>

</div>
