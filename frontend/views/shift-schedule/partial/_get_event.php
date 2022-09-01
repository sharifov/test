<?php

use common\components\grid\DateTimeColumn;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $event UserShiftSchedule */
/* @var $userTimeZone string */
/* @var ActiveDataProvider $dataProvider */
/* @var UserTaskSearch $searchModel */

$hours = round($event->uss_duration / 60) ?: 1;
$tsStartUtc = strtotime($event->uss_start_utc_dt);
$tsEndUtc = strtotime($event->uss_end_utc_dt);

?>
<div class="shift-schedule-event-view">

    <div class="col-md-9 text-left">
    <h5>
        <span class="Time Line Type">
            <?php echo $event->shiftScheduleType->getIconLabel()?>
            <?php echo Html::encode($event->getScheduleTypeTitle())?>,
        </span>
        &nbsp;
        <span title="User">
            <i class="fa fa-user"></i> <?php echo Html::encode($event->user->username)?>
        </span>
    </h5>
    </div>
    <div class="col-md-3 text-right">
        <h6><span title="Status"><?php echo Html::encode($event->getStatusName())?></span></h6>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr class="text-center">
                <th scope="col">Start</th>
                <th scope="col">Duration</th>
                <th scope="col">End</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>
                    <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone(
                        $tsStartUtc,
                        $userTimeZone,
                        'php: d-M-Y'
                    )?></h6>
                    <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone(
                        $tsStartUtc,
                        $userTimeZone,
                        'php: H:i'
                    )?></h4>
                </td>
                <td style="width: 400px">
                    <div class="table-responsive" style="width: 400px">
                        <table class="table">
                            <thead>
                            <tr style="background: <?=Html::encode($event->shiftScheduleType->sst_color)?>">
                                <?php for ($i = 0; $i < $hours; $i++) : ?>
                                <td style="color: #FFFFFF">
                                    <?= Yii::$app->formatter->asDateTimeByUserTimezone($tsStartUtc + (60 * 60 * $i), $userTimeZone, 'php:H') ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <strong>
                        Duration Time: <?php echo Yii::$app->formatter->asDuration($event->uss_duration * 60) ?>
                    </strong>
                </td>
                <td>
                    <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone(
                        $tsEndUtc,
                        $userTimeZone,
                        'php: d-M-Y'
                    )?></h6>
                    <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone(
                        $tsEndUtc,
                        $userTimeZone,
                        'php: H:i'
                    )?></h4>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="col-md-6 text-left">
        <span title="TimeZone">
            <i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone?>
        </span>
    </div>

    <div class="col-md-6 text-right">
        <span title="Created / Updated">
            <i class="fa fa-calendar"></i>
            <?= Yii::$app->formatter->asDatetime(strtotime($event->uss_updated_dt ?: $event->uss_created_dt))?>
        </span>
    </div>

    <div class="col-md-12">
        <?php if ($event->uss_description) : ?>
          <div>Description: <?= Html::encode($event->uss_description) ?></div>
        <?php endif; ?>
    </div>

    <?php if ($dataProvider->getTotalCount()) : ?>
    <div class="row">
        <div class="col-md-12">
        <?php Pjax::begin(['id' => 'pjax-user-schedule-event-timeline-tasklist',
            'enableReplaceState' => false, 'enablePushState' => false]); ?>
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-bars"></i> Task List</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">


                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'layout' => "{errors}\n{summary}\n{items}\n{pager}",
                    'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
                    'rowOptions' => static function (UserTask $model) {
                        if ($model->isDelay()) {
                            return [
                                'class' => 'bg-info'
                            ];
                        }
                        if ($model->isDeadline()) {
                            return [
                                'class' => 'danger'
                            ];
                        }
                        return [];
                    },
                    'columns' => [
                        [
                            'attribute' => 'ut_id',
                            'value' => static function (UserTask $model) {
                                return $model->ut_id;
                            },
                            'options' => ['style' => 'width:80px']
                        ],
                        [
                            'attribute' => 'ut_priority',
                            'value' => static function (UserTask $model) {
                                return UserTaskHelper::priorityLabel($model->ut_priority);
                            },
                            'format' => 'raw',
                            'filter' => UserTask::PRIORITY_LIST,
                        ],

                        [
                            'attribute' => 'ut_status_id',
                            'value' => static function (UserTask $model) {
                                $result = UserTaskHelper::statusLabel($model->ut_status_id);

                                if ($model->isDeadline()) {
                                    $result .= '<p class="text-center mt-2 text-danger"><i class="fa fa-times-circle"></i></p>';
                                }

                                return $result;
                            },
                            'format' => 'raw',
                            'filter' => UserTask::STATUS_LIST,
                        ],

                        [
                            'attribute' => 'ut_task_list_id',
                            'label' => 'Task Name',
                            'value' => static function (UserTask $model) {
                                if (!$model->ut_task_list_id) {
                                    return '-'; //Yii::$app->formatter->nullDisplay;
                                }
                                return Html::tag(
                                    'span',
                                    $model->taskList->tl_title ?: '-',
                                    ['title' => 'Task List ID: ' . $model->ut_task_list_id]
                                );
                            },
                            'format' => 'raw',
                        ],

                        [
                            'attribute' => 'ut_target_object',
                            'label' => 'Object',
                            'value' => static function (UserTask $model) {
                                if (!$model->ut_target_object) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                return $model->ut_target_object;
                            },
                            'filter' => TargetObject::TARGET_OBJ_LIST,
                            'format' => 'raw',
                        ],

                        [
                            'attribute' => 'ut_target_object_id',
                            'label' => 'Target',
                            'value' => static function (UserTask $model) {
                                return TargetObject::getTargetLink(
                                    $model->ut_target_object,
                                    $model->ut_target_object_id
                                );
                            },
                            // 'filter' => TargetObject::TARGET_OBJ_LIST,
                            'format' => 'raw',
                        ],
                        //'ut_target_object_id',

                        [
                            'label' => 'Duration',
                            'value' => static function (UserTask $model) {
                                return UserTaskHelper::getDuration($model->ut_start_dt, $model->ut_end_dt);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'label' => 'Delay',
                            'value' => static function (UserTask $model) {
                                return $model->isDelay() ?
                                    UserTaskHelper::getDelayTimer($model->ut_start_dt, $model->ut_end_dt) :
                                    '-';
                            },
                            'format' => 'raw',
                        ],

//                        [
//                            'class' => DateTimeColumn::class,
//                            'limitEndDay' => false,
//                            'attribute' => 'ut_start_dt',
//                            'format' => 'byUserDateTimeAndUTC',
//                        ],
                        [
                            'label' => 'Deadline',
                            'value' => static function (UserTask $model) {
                                if ($model->isProcessing()) {
                                    return $model->isDeadline() ? Html::tag(
                                        'span',
                                        'Deadline',
                                        ['title' => \Yii::$app->formatter->asRelativeTime(strtotime($model->ut_end_dt)),
                                            'class' => 'badge badge-danger']
                                    ) :
                                        UserTaskHelper::getDeadlineTimer($model->ut_start_dt, $model->ut_end_dt);
                                }

                                return '-';
                            },
                            'format' => 'raw',
                        ],
//                        [
//                            'class' => DateTimeColumn::class,
//                            'limitEndDay' => false,
//                            'attribute' => 'ut_end_dt',
//                            'format' => 'byUserDateTimeAndUTC',
//                        ],
                    ],
                ]); ?>
            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>
    </div>
    <?php endif; ?>

</div>
