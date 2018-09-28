<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Lead Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'lt_lead_id',
            [
                'label' => 'Lead UID',
                'attribute' => 'lt_lead_id',
                'value' => function(\common\models\LeadTask $model) {
                    return Html::a($model->ltLead->uid, ['lead/processing/' . $model->lt_lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'filter' => false
            ],

            [
                'label' => 'Task',
                'attribute' => 'lt_task_id',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltTask ? $model->ltTask->t_name : '-';
                },
                'filter' => \common\models\Task::getList()
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
                'filter' => \common\models\Employee::getList()
            ],

            'lt_date',
            'lt_notes',
            'lt_completed_dt',
            'lt_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
