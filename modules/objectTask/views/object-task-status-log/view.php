<?php

use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskStatusLog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskStatusLog */

$this->title = $model->otsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="object-task-status-log-view col-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'otsl_id' => $model->otsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'otsl_id' => $model->otsl_id], [
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
            'otsl_id',
            'otsl_ot_uuid',
            [
                'attribute' => 'otsl_old_status',
                'value' => static function (ObjectTaskStatusLog $model) {
                    return ObjectTask::STATUS_LIST[$model->otsl_old_status] ?? ' - ';
                },
            ],
            [
                'attribute' => 'otsl_new_status',
                'value' => static function (ObjectTaskStatusLog $model) {
                    return ObjectTask::STATUS_LIST[$model->otsl_new_status] ?? ' - ';
                },
            ],
            'otsl_description',
            'otsl_created_user_id:username',
            'otsl_created_dt',
        ],
    ]) ?>

</div>
