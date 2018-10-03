<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadTask */

$this->title = $model->lt_lead_id.' '.$model->lt_task_id.' '.$model->lt_date;
$this->params['breadcrumbs'][] = ['label' => 'Lead Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id, 'lt_date' => $model->lt_date], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id, 'lt_date' => $model->lt_date], [
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
            'lt_lead_id',
            [
                'label' => 'Task',
                'attribute' => 'lt_task_id',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltTask ? $model->ltTask->t_name : '-';
                },
            ],

            //'lt_task_id',
            //'ltTask.t_name',
            //'lt_user_id',
            //'ltUser.username',

            [
                'label' => 'Employee',
                'attribute' => 'lt_user_id',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltUser ? $model->ltUser->username : '-';
                },
            ],
            'lt_date',
            'lt_notes',
            //'lt_completed_dt',
            //'lt_updated_dt',
            [
                'attribute' => 'lt_completed_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_completed_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_completed_dt)) : '-';
                },
                'format' => 'html',
            ],

            [
                'attribute' => 'lt_updated_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_updated_dt)) : '-';
                },
                'format' => 'html',
            ],
        ],
    ]) ?>

</div>
