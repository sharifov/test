<?php

use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskStatusLog */

$this->title = $model->utsl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Task Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-task-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'utsl_id' => $model->utsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'utsl_id' => $model->utsl_id], [
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
            'utsl_id',
            'utsl_ut_id',
            'utsl_description',
            [
                'attribute' => 'utsl_old_status',
                'value' => static function (UserTaskStatusLog $model) {
                    return UserTaskHelper::statusLabel($model->utsl_old_status);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            [
                'attribute' => 'utsl_new_status',
                'value' => static function (UserTaskStatusLog $model) {
                    return UserTaskHelper::statusLabel($model->utsl_new_status);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            'utsl_created_user_id:userNameWithId',
            'utsl_created_dt',
        ],
    ]) ?>

</div>
