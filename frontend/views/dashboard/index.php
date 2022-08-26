<?php

use common\models\Employee;
use modules\featureFlag\FFlag;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use src\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchLeadTask common\models\search\LeadTaskSearch */
/* @var $dp1 yii\data\ActiveDataProvider */
/* @var $dp2 yii\data\ActiveDataProvider */
/* @var $dp3 yii\data\ActiveDataProvider */

/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$bundle = \frontend\assets\TimelineAsset::register($this);

$this->title = 'Dashboard - Agent';

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
/** @fflag FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE, Switch new Shift Enable */
$canNewShift = \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE);


?>
<?php /*<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>*/?>

<?php

//$date = date('Y-m-d H:i', strtotime("+1 days"));

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>
    <div class="site-index">
        <h1><?=$this->title?></h1>
        <div class="row">
            <div class="col-md-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Server Date Time (UTC)</th>
                            <td><i class="fa fa-calendar"></i> <?= date('Y-M-d [H:i]')?></td>
                        </tr>
                        <tr>
                            <th>Current Time Zone</th>
                            <td><i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone?></td>
                        </tr>
                        <tr>
                            <th>Local Date Time</th>
                            <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(time())?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>My Username:</th>
                            <td><i class="fa fa-user"></i> <?= Yii::$app->user->identity->username?> (<?=Yii::$app->user->id?>)</td>
                        </tr>
                        <tr>
                            <th>My Role:</th>
                            <td><?=implode(', ', Yii::$app->user->identity->getRoles())?></td>
                        </tr>
                        <tr>
                            <th>My User Groups:</th>
                            <td><i class="fa fa-users"></i>
                                <?php
                                $groupsValue = '';
                                if ($groupsModel =  Yii::$app->user->identity->ugsGroups) {
                                    $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');
                                    $groupsValueArr = [];
                                    foreach ($groups as $group) {
                                        $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                                    }
                                    $groupsValue = implode(' ', $groupsValueArr);
                                }
                                echo $groupsValue;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>My Project Access:</th>
                            <td><i class="fa fa-list"></i>
                                <?php
                                //\yii\helpers\VarDumper::dump(Yii::$app->user->identity->projects, 10, true);

                                $projectsValue = '';

                                //$projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
                                $projectList = Yii::$app->user->identity->projects;

                                if ($projectList) {
                                    $groupsValueArr = [];
                                    foreach ($projectList as $project) {
                                        $groupsValueArr[] = Html::tag('span', Html::encode($project->name), ['class' => 'label label-default']);
                                    }
                                    $projectsValue = implode(' ', $groupsValueArr);
                                }
                                echo $projectsValue;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>My Product Types:</th>
                            <td><i class="fa fa-list"></i>
                                <?php
                                if ($productTypeList = Yii::$app->user->identity->productType) {
                                    $productTypeValue = '';
                                    foreach ($productTypeList as $productType) {
                                        $productTypeValue .= Html::tag('span', Html::encode($productType->pt_name), ['class' => 'label label-default']) . ' ';
                                    }
                                    echo $productTypeValue;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-3">
                <?php
                /** @var \common\models\UserParams $modelUserParams */
                $modelUserParams = Yii::$app->user->identity->userParams;
                if ($modelUserParams) {
                    echo \yii\widgets\DetailView::widget([
                        'model' => $modelUserParams ?? null,
                        'attributes' => [
                            /*[
                                'attribute' => 'up_base_amount',
                                'value' => function(\common\models\UserParams $model) {
                                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                                },
                            ],
                            [
                                'attribute' => 'up_commission_percent',
                                'value' => function(\common\models\UserParams $model) {
                                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
                                },

                            ],*/
                            'up_bonus_active:boolean',
                            'up_timezone',
                            ['attribute' => 'up_work_start_tm', 'visible' => !$canNewShift],
                            ['attribute' => 'up_work_minutes', 'visible' => !$canNewShift],

                            'up_inbox_show_limit_leads',
                            'up_default_take_limit_leads',
                            'up_min_percent_for_take_leads',

                            /*[
                                'attribute' => 'up_updated_dt',
                                'value' => function(\common\models\UserParams $model) {
                                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                                },
                                'format' => 'raw',
                            ],*/

                        ],
                    ]);
                }
                ?>
            </div>

            <div class="col-md-3">
                <?php
                $taskSummary = Yii::$app->user->identity->getCurrentShiftTaskInfoSummary();
                //\yii\helpers\VarDumper::dump($taskSummary, 10, true);
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Current Shift All tasks</th>
                            <td><?=$taskSummary['allTasksCount']?></td>
                        </tr>
                        <tr>
                            <th>Current Shift Completed tasks</th>
                            <td><?=$taskSummary['completedTasksCount']?></td>
                        </tr>
                        <tr>
                            <th>Current Shift task progress</th>
                            <td style="width: 50%">
                                <div class="progress" title="<?=$taskSummary['completedTasksPercent']?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: <?=$taskSummary['completedTasksPercent']?>%;">
                                        <?=$taskSummary['completedTasksPercent']?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($modelUserParams) :
            $js = <<<JS
    //google.charts.load('current', {packages: ['corechart', 'bar']});
    $("#myTimeline").timeline({
        type            : "bar",
        rows            : 1,
        //rowHeight       : 80,
        height          : "auto"
  //      startDatetime   : "current"
    });
JS;
            $this->registerJs($js, \yii\web\View::POS_READY);

            ?>

            <h3>My Shift Timeline</h3>
            <!-- Timeline Block -->
            <div id="myTimeline">
                <ul class="timeline-events">
                    <?php
                    if ($canNewShift) :
                        ?>
                        <?php
                        $firstUserShiftSchedule = UserShiftScheduleQuery::getQueryForNextShiftsByUserId(
                            $user->id,
                            (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
                        )
                            ->select(['uss_start_utc_dt', 'uss_end_utc_dt', 'uss_duration'])
                            ->limit(2)
                            ->asArray()
                            ->all();
                        if (count($firstUserShiftSchedule) == 0) :?>
                            <li data-timeline-node="{ start:'01.01.1970 00:00', end:'01.01.1970 00:00'}"></li>

                        <?php endif;

                        foreach ($firstUserShiftSchedule as $key => $event) {
                            $startTime = \Yii::$app->formatter->asDateTimeByUserTimezone(strtotime($event['uss_start_utc_dt']), ($modelUserParams->up_timezone ?: 'UTC'), 'php:Y-m-d H:i');
                            echo $startTime;
                            $endTime = \Yii::$app->formatter->asDateTimeByUserTimezone(strtotime($event['uss_end_utc_dt']), ($modelUserParams->up_timezone ?: 'UTC'), 'php:Y-m-d H:i');

                            $params = "row:1";
                            if ($key === 0) {
                                $params = "bgColor:'rgb(137, 201, 151)',color:'#fff',row:1,extend:{'post_id':1,'permalink':'https://google.com/'}";
                            } ?>
                            <li data-timeline-node="{ start:'<?= $startTime ?>',end:'<?= $endTime ?>',content:'<?= $key + 1 ?> shift', <?= $params ?>}">
                                <?= date('d-M [H:i]', strtotime($startTime)) ?>
                                ........ <?= date('d-M [H:i]', strtotime($endTime)) ?> .....
                                (<?= round($event['uss_duration'] / 60, 1) ?> hours)
                            </li>
                        <?php } ?>
                    <?php else : ?>
                        <?php
                        $currentDateTS = strtotime(Yii::$app->formatter->asDate(time()));
                        $startTime = date('Y-m-d ' . $modelUserParams->up_work_start_tm);
                        echo $startTime;
                        $endTime = date('Y-m-d H:i', strtotime($startTime) + ($modelUserParams->up_work_minutes * 60));
                        ?>
                        <li data-timeline-node="{ start:'<?= $startTime ?>',end:'<?= $endTime ?>',content:'1 shift',bgColor:'rgb(137, 201, 151)',color:'#fff',row:1,extend:{'post_id':1,'permalink':'https://google.com/'} }"><?= date('d-M [H:i]', strtotime($startTime)) ?>
                            ........ <?= date('d-M [H:i]', strtotime($endTime)) ?> .....
                            (<?= round($modelUserParams->up_work_minutes / 60, 1) ?> hours)
                        </li>

                        <?php
                        $currentDateTS = strtotime(Yii::$app->formatter->asDate(strtotime("+1 day")));
                        $startTime = date('Y-m-d ' . $modelUserParams->up_work_start_tm, $currentDateTS);
                        echo $startTime;
                        $endTime = date('Y-m-d H:i', strtotime($startTime) + ($modelUserParams->up_work_minutes * 60));
                        ?>
                        <li data-timeline-node="{ start:'<?= $startTime ?>',end:'<?= $endTime ?>',content:'2 shift',row:1 }"><?= date('d-M [H:i]', strtotime($startTime)) ?>
                            ........ <?= date('d-M [H:i]', strtotime($endTime)) ?> .....
                            (<?= round($modelUserParams->up_work_minutes / 60, 1) ?> hours)
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Timeline Event Detail View Area (optional) -->
            <div class="timeline-event-view"></div>

        <?php endif; ?>

        <br>

        <?php
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],

            [
                //'label' => 'Lead UID',
                'attribute' => 'lt_lead_id',
                'value' => function (\common\models\LeadTask $model) {
                    return Html::a($model->lt_lead_id, ['lead/view', 'gid' => $model->ltLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                //'filter' => false
            ],

            [
                'label' => 'Task',
                'attribute' => 'lt_task_id',
                'value' => function (\common\models\LeadTask $model) {

                    $taskIcon = '';
                    if ($model->ltTask && $model->ltTask->t_key === 'call2') {
                        $call2DelayTime = Yii::$app->params['lead']['call2DelayTime']; //(2 * 60 * 60);

                        $taskCall1 = \common\models\LeadTask::find()->where(['lt_user_id' => $model->lt_user_id, 'lt_lead_id' => $model->lt_lead_id, 'lt_date' => $model->lt_date, 'lt_task_id' => 1])->one();

                        if ($taskCall1) {
                            if ((strtotime($taskCall1->lt_completed_dt) + $call2DelayTime) <= time()) {
                                $call2TaskEnable = true;
                            } else {
                                $taskIcon = '<br><span class="label label-default">Call after <i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asDatetime(strtotime($taskCall1->lt_completed_dt) + $call2DelayTime) . '</span>';
                                //'<i class="fa fa-clock-o" title="Next call '.Yii::$app->formatter->asDatetime(strtotime($taskCall1->lt_completed_dt) + $call2DelayTime).'"></i> ';
                            }
                        }
                        //$taskIcon = '<i class="fa fa-clock-o"></i>';
                    }


                    return $model->ltTask ? '<span style="font-size: 13px" title="' . Html::encode($model->ltTask->t_description) . '" class="label label-info">' . Html::encode($model->ltTask->t_name) . '</span>' . $taskIcon . '' : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Task::getList()
            ],

            [
                'label' => 'Timer',
                'value' => function (\common\models\LeadTask $model) {

                    $cdTime = 0;
                    if ($model->ltTask && $model->ltTask->t_key === 'call2') {
                        $call2DelayTime = Yii::$app->params['lead']['call2DelayTime'];

                        $taskCall1 = \common\models\LeadTask::find()->where(['lt_user_id' => $model->lt_user_id, 'lt_lead_id' => $model->lt_lead_id, 'lt_date' => $model->lt_date, 'lt_task_id' => 1])->one();

                        if ($taskCall1 && (strtotime($taskCall1->lt_completed_dt) + $call2DelayTime) > time()) {
                            $cdTime = strtotime($taskCall1->lt_completed_dt) + $call2DelayTime;
                        }
                    }

                    $elapsedTime = $cdTime - time();

                    return $elapsedTime > 0 ? '<div data-elapsed="' . $elapsedTime . '" data-countdown="' . date('Y-m-d H:i:s', $cdTime) . '"></div>' : '-';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 80px']
            ],

            [
                'attribute' => 'lt_notes',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->lt_notes ? $model->lt_notes : '-';
                },
            ],

            [
                'label' => 'Lead status',
                'attribute' => 'ltLead.status',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->ltLead ? $model->ltLead->getStatusName() : '-';
                },
                'format' => 'raw'
            ],

            [
                //'attribute' => 'client_id',
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function (\common\models\LeadTask $model) {

                    if ($model->ltLead->client) {
                        $clientName = $model->ltLead->client->first_name . ' ' . $model->ltLead->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '-';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }
                    } else {
                        $clientName = '-';
                    }

                    return $clientName;
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                //'attribute' => 'client_id',
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function (\common\models\LeadTask $model) {
                    if ($model->ltLead) {
                        $clientTime = ClientTimeFormatter::format($model->ltLead->getClientTime2(), $model->ltLead->offset_gmt);
                    } else {
                        $clientTime = '-';
                    }
                    return $clientTime;
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],



            [
                'label' => 'Segments',
                'value' => function (\common\models\LeadTask $model) {

                    $segments = $model->ltLead->leadFlightSegments;
                    $segmentData = [];
                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            //$segmentData[] = ($sk + 1).'. <code>'.Html::a($segment->origin.'->'.$segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                            $segmentData[] = ($sk + 1) . '. <code>' . $segment->origin . '->' . $segment->destination . '</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return '' . $segmentStr . '';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'options' => ['style' => 'width:140px'],
            ],


            [
                'label' => 'Cabin',
                'attribute' => 'leads.cabin',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->ltLead->getCabinClassName();
                },
            ],

            [
                'label' => 'Adults',
                'attribute' => 'leads.adults',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->ltLead->adults ?: 0;
                },
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'label' => 'Children',
                'attribute' => 'leads.children',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->ltLead->children ?: '-';
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Infants',
                'attribute' => 'leads.infants',
                'value' => function (\common\models\LeadTask $model) {
                    return $model->ltLead->infants ?: '-';
                },
                'contentOptions' => ['class' => 'text-center'],
            ],


            [
                'label' => 'Lead created',
                'attribute' => 'ltLead.created',
                'value' => function (\common\models\LeadTask $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ltLead->created));
                },
                'format' => 'raw',
            ],

            [
                'label' => 'Lead pending time',
                //'attribute' => 'ltLead.created',
                'value' => function (\common\models\LeadTask $model) {
                    $time = Yii::$app->formatter->asRelativeTime(strtotime($model->ltLead->created));
                    return $time; //'<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ltLead->created));
                },
                'format' => 'raw',
            ],

            /*[
                'attribute' => 'lt_completed_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_completed_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_completed_dt)) : '-';
                },
                'format' => 'html',
            ],*/
        ];
        ?>

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li>
                        <a data-toggle="tab" href="#tab-1">
                            <i class="fa fa-calendar-times-o"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime("-1 days")))?> <span class="label label-default">previous</span>
                        </a>
                    </li>
                    <li class="active">
                        <a data-toggle="tab" href="#tab-2" style="background-color: #dff0d8">
                            <i class="fa fa-calendar"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(time()))?> <span class="label label-success">current</span>
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#tab-3" style="background-color:">
                            <i class="fa fa-calendar-minus-o"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime("+1 days")))?> <span class="label label-warning">next</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade in">
                        <?php Pjax::begin(); ?>
                        <h4>To Do Task List <span class="label label-default">previous</span> (<?=Yii::$app->formatter->asDate(strtotime("-1 days"))?>):</h4>
                        <?= GridView::widget([
                            'dataProvider' => $dp1,
                            'filterModel' => $searchLeadTask,
                            //'tableOptions' => ['class' => 'table table-bordered table-condensed table-striped table-hover'],
                            'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                                if ($model->lt_completed_dt) {
                                    return ['class' => 'success'];
                                }
                            },
                            'columns' => $columns,
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>

                    <div id="tab-2" class="tab-pane fade in active show">
                        <?php Pjax::begin(); ?>
                        <h4>To Do Task List <span class="label label-success">current</span> (<?=Yii::$app->formatter->asDate(time())?>):</h4>
                        <?= GridView::widget([
                            'dataProvider' => $dp2,
                            'filterModel' => $searchLeadTask,
                            'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                                if ($model->lt_completed_dt) {
                                    return ['class' => 'success'];
                                }
                            },
                            'columns' => $columns,
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>

                    <div id="tab-3" class="tab-pane fade in">
                        <?php Pjax::begin(); ?>
                        <h4>To Do Task List <span class="label label-warning">next</span> (<?=Yii::$app->formatter->asDate(strtotime("+1 days"))?>):</h4>
                        <?= GridView::widget([
                            'dataProvider' => $dp3,
                            'filterModel' => $searchLeadTask,
                            'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                                if ($model->lt_completed_dt) {
                                    return ['class' => 'success'];
                                }
                            },
                            'columns' => $columns,
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php Pjax::begin(); ?>
        <div class="card card-default">
            <div class="card-header">My Stats <?=$searchModel->timeRange ? '(' . $searchModel->timeRange . ')' : ''?></div>
            <div class="card-body">

                <div class="row">

                    <?php $form = ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]); ?>

                    <div class="col-md-3">
                        <?php
                        echo  \kartik\daterange\DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'timeRange',
                            'useWithAddon' => true,
                            'presetDropdown' => true,
                            'hideInput' => true,
                            'convertFormat' => true,
                            'startAttribute' => 'timeStart',
                            'endAttribute' => 'timeEnd',
                            'pluginOptions' => [
                                'timePicker' => true,
                                'timePickerIncrement' => 1,
                                'timePicker24Hour' => true,
                                'locale' => ['format' => 'Y-m-d H:i']
                            ]
                        ]);
                        ?>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-primary']) ?>
                        <?php //= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                        if ($model->isDeleted()) {
                            return ['class' => 'danger'];
                        }
                    },
                    'columns' => [
                        /*[
                            'attribute' => 'id',
                            'contentOptions' => ['class' => 'text-center'],
                            'options' => ['style' => 'width:60px'],
                        ],*/
                        [
                            'attribute' => 'username',
                            'value' => static function (\common\models\Employee $model) {
                                return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($model->username);
                            },
                            'format' => 'raw',
                            //'contentOptions' => ['title' => 'text-center'],
                            'options' => ['style' => 'width:180px'],
                        ],

                        /*[
                            //'attribute' => 'username',
                            'label' => 'Role',
                            'value' => static function (\common\models\Employee $model) {
                                $roles = $model->getRoles();
                                return $roles ? implode(', ', $roles) : '-';
                            },
                            'options' => ['style' => 'width:150px'],
                            //'format' => 'raw'
                        ],*/

                        /*'email:email',
                        [
                            'attribute' => 'status',
                            'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                            'value' => static function (\common\models\Employee $model) {
                                return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                            },
                            'format' => 'html'
                        ],*/

                        /*[
                            'label' => 'User Groups',
                            'attribute' => 'user_group_id',
                            'value' => static function (\common\models\Employee $model) {

                                $groups = $model->getUserGroupList();
                                $groupsValueArr = [];

                                foreach ($groups as $group) {
                                    $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                                }

                                $groupsValue = implode(' ', $groupsValueArr);

                                return $groupsValue;
                            },
                            'format' => 'raw',
                            'filter' => $user->isAdmin() ? \common\models\UserGroup::getList() : $user->getUserGroupList()
                        ],*/

                        [
                            'label' => 'Tasks Result for Period',
                            'value' => function (\common\models\Employee $model) use ($searchModel) {
                                return $model->getTaskStats($searchModel->timeStart, $searchModel->timeEnd);
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-left'],
                            /*'filter' => \kartik\daterange\DateRangePicker::widget([
                                'model'=> $searchModel,
                                'attribute' => 'date_range',
                                //'name'=>'date_range',
                                'useWithAddon'=>true,
                                //'value'=>'2015-10-19 12:00 AM - 2015-11-03 01:00 PM',
                                'presetDropdown'=>true,
                                'hideInput'=>true,
                                'convertFormat'=>true,
                                'startAttribute' => 'datetime_start',
                                'endAttribute' => 'datetime_end',
                                //'startInputOptions' => ['value' => date('Y-m-d', strtotime('-5 days'))],
                                //'endInputOptions' => ['value' => '2017-07-20'],
                                'pluginOptions'=>[
                                    'timePicker'=> false,
                                    'timePickerIncrement'=>15,
                                    'locale'=>['format'=>'Y-m-d']
                                ]
                            ])*/
                            //'options' => ['style' => 'width:200px'],

                        ],
                        [
                            'label' => 'Processing',
                            'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_PROCESSING], $searchModel->timeStart, $searchModel->timeEnd);
                                /*return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_PROCESSING,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';*/
                                return $cnt ?: '-';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width: 100px']
                        ],
                        [
                            'label' => 'Booked',
                            'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_BOOKED], $searchModel->timeStart, $searchModel->timeEnd);
                                /*return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';*/
                                return $cnt ?: '-';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width: 100px']
                        ],
                        [
                            'label' => 'Sold',
                            'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_SOLD], $searchModel->timeStart, $searchModel->timeEnd);
                                /*return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_SOLD,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';*/
                                return $cnt ?: '-';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width: 100px']
                        ],
                        [
                            'label' => 'Follow Up',
                            'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_FOLLOW_UP], $searchModel->timeStart, $searchModel->timeEnd);
                                /*return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_FOLLOW_UP,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';*/
                                return $cnt ?: '-';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width: 100px']
                        ],
                        [
                            'label' => 'Trash',
                            'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_TRASH], $searchModel->timeStart, $searchModel->timeEnd);
                                /*return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_TRASH,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';*/
                                return $cnt ?: '-';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width: 100px']
                        ]
                    ]
                ])
?>
            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>

<?php
$js = '
function initCountDown()
{
    $("[data-countdown]").each(function() {
      var $this = $(this), finalDate = $(this).data("countdown");
      var elapsedTime = $(this).data("elapsed");
          
        var seconds = new Date().getTime() + (elapsedTime * 1000);
        $this.countdown(seconds, function(event) {
            //var totalHours = event.offset.totalDays * 24 + event.offset.hours;
            $(this).html(event.strftime(\'%H:%M:%S\'));
        });
      
        /*$this.countdown(seconds, {elapse: false}).on(\'update.countdown\', function(event) {
            var $this = $(this);
            $this.html(event.strftime(\'To end: <span>%H:%M:%S</span>\'));
        });*/
      
    });
}

$(document).on(\'pjax:end\', function() {
    initCountDown();    
});

initCountDown();

';

$this->registerJs($js);