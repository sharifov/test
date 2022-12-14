<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogCrudSearch;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReasonQuery;
use modules\qaTask\src\grid\columns\QaTaskColumn;
use modules\qaTask\src\grid\columns\QaTaskActionColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use modules\qaTask\src\helpers\formatters\QaTaskStatusReasonFormatter;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserSelect2Column;
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

    <?php Pjax::begin(['scrollTo' => 0]); ?>
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
                    return $log->tsl_reason_id ? $log->reason->tar_name : null;
                },
                'filter' => QaTaskStatusReasonFormatter::formatListByFullDescription(),
            ],
            'tsl_description',
            [
                'class' => QaTaskActionColumn::class,
                'attribute' => 'tsl_action_id',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tsl_assigned_user_id',
                'relation' => 'assignedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tsl_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
