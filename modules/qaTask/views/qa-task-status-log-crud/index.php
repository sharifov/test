<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogCrudSearch;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReasonQuery;
use modules\qaTask\src\grid\columns\QaTaskColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusActionColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use modules\qaTask\src\helpers\formatters\QaTaskStatusReasonFormatter;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\DurationColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel QaTaskStatusLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tsl_id',
            [
                'class' => QaTaskColumn::class,
                'attribute' => 'tsl_task_id',
                'relation' => 'task',
            ],
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 'tsl_start_status_id',
            ],
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 'tsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'tsl_duration',
                'startAttribute' => 'tsl_start_dt',
            ],
            [
                'attribute' => 'tsl_reason_id',
                'value' => static function (QaTaskStatusLog $log) {
                    return $log->tsl_reason_id ? $log->reason->tsr_name : null;
                },
                'filter' => QaTaskStatusReasonFormatter::formatListByFullDescription(),
            ],
            'tsl_description',
            [
                'class' => QaTaskStatusActionColumn::class,
                'attribute' => 'tsl_action_id',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tsl_assigned_user_id',
                'relation' => 'assignedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'tsl_created_user_id',
                'relation' => 'createdUser',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
