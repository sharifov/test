<?php

use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogSearch;
use modules\qaTask\src\grid\columns\QaTaskActionColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\DurationColumn;
use sales\yii\grid\UserColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel QaTaskStatusLogSearch */

?>

<div class="qa-task-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
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
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'tsl_reason_id',
                'value' => static function (QaTaskStatusLog $log) {
                    return $log->tsl_reason_id ? $log->reason->tar_name : null;
                }
            ],
            [
                'attribute' => 'tsl_description',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => QaTaskActionColumn::class,
                'attribute' => 'tsl_action_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'assignedUser',
                'attribute' => 'tsl_assigned_user_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'tsl_created_user_id',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
