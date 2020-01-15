<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$bundle = \frontend\assets\TimelineAsset::register($this);
$this->title = 'Dashboard - Supervision';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="site-index">

    <h1><?=$this->title?></h1>
    <div class="row">
        <div class="col-md-3">
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
                    <th>Formatted Local Date Time</th>
                    <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(time())?></td>
                </tr>
            </table>

        </div>

        <div class="col-md-3">
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
                        if( $groupsModel =  Yii::$app->user->identity->ugsGroups) {
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
                        $projectsValue = '';

                        $projectList = Yii::$app->user->identity->projects;

                        if($projectList) {

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
            </table>

        </div>

        <div class="col-md-3">
            <?php

            /** @var \common\models\UserParams $modelUserParams */
            $modelUserParams = Yii::$app->user->identity->userParams;
            if($modelUserParams) {
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
                        'up_work_start_tm',
                        'up_work_minutes',
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

    </div>

    <?php if($modelUserParams):

        $js = <<<JS
    //google.charts.load('current', {packages: ['corechart', 'bar']});
    $("#myTimeline").timeline({
        type            : "bar",
        rows            : 1,
        //rowHeight       : 80,
        height          : "auto",
        langsDir        : "./js/jquery.timeline-master/dist/langs/",
        httpLnaguage    : true

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
                $currentDateTS = strtotime(Yii::$app->formatter->asDate(time()));
                $startTime = date('Y-m-d '.$modelUserParams->up_work_start_tm);
                echo $startTime;
                $endTime = date('Y-m-d H:i', strtotime($startTime) + ($modelUserParams->up_work_minutes * 60));
                ?>
                <li data-timeline-node="{ start:'<?=$startTime?>',end:'<?=$endTime?>',content:'1 shift',bgColor:'rgb(137, 201, 151)',color:'#fff',row:1,extend:{'post_id':1,'permalink':'https://google.com/'} }"><?=date('d-M [H:i]', strtotime($startTime))?> ........ <?=date('d-M [H:i]', strtotime($endTime))?> ..... (<?=round($modelUserParams->up_work_minutes/60, 1)?> hours)</li>

                <?php
                $currentDateTS = strtotime(Yii::$app->formatter->asDate(strtotime("+1 day")));
                $startTime = date('Y-m-d '.$modelUserParams->up_work_start_tm, $currentDateTS);
                echo $startTime;
                $endTime = date('Y-m-d H:i', strtotime($startTime) + ($modelUserParams->up_work_minutes * 60));
                ?>
                <li data-timeline-node="{ start:'<?=$startTime?>',end:'<?=$endTime?>',content:'2 shift',row:1 }"><?=date('d-M [H:i]', strtotime($startTime))?> ........ <?=date('d-M [H:i]', strtotime($endTime))?> ..... (<?=round($modelUserParams->up_work_minutes/60, 1)?> hours)</li>

            </ul>
        </div>

        <!-- Timeline Event Detail View Area (optional) -->
        <div class="timeline-event-view"></div>



    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <div class="card card-default">
        <div class="card-header">Agents Stats <?=$searchModel->timeRange ? '(' . $searchModel->timeRange . ')' : ''?></div>
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
                        'model'=> $searchModel,
                        'attribute' => 'timeRange',
                        //'name'=>'date_range',
                        'useWithAddon'=>true,
                        //'value'=>'2015-10-19 12:00 AM - 2015-11-03 01:00 PM',
                        'presetDropdown'=>true,
                        'hideInput'=>true,
                        'convertFormat'=>true,
                        'startAttribute' => 'timeStart',
                        'endAttribute' => 'timeEnd',
                        //'startInputOptions' => ['value' => date('Y-m-d', strtotime('-5 days'))],
                        //'endInputOptions' => ['value' => '2017-07-20'],
                        'pluginOptions'=>[
                            'timePicker'=> true,
                            'timePickerIncrement'=>1,
                            'timePicker24Hour' => true,
                            'locale'=>['format'=>'Y-m-d H:i']
                        ]
                    ]);
                ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-primary']) ?>
                    <?//= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                    if ($model->isDeleted()) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['class' => 'text-center'],
                        'options' => ['style' => 'width:60px'],
                    ],
                    [
                        'attribute' => 'username',
                        'value' => static function (\common\models\Employee $model) {
                            return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                        },
                        'format' => 'raw',
                        //'contentOptions' => ['title' => 'text-center'],
                        'options' => ['style' => 'width:180px'],
                    ],

                    [
                        //'attribute' => 'username',
                        'label' => 'Role',
                        'value' => static function (\common\models\Employee $model) {
                            $roles = $model->getRoles();
                            return $roles ? implode(', ', $roles) : '-';
                        },
                        'options' => ['style' => 'width:150px'],
                        //'format' => 'raw'
                    ],

                    /*'email:email',
                    [
                        'attribute' => 'status',
                        'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                        'value' => static function (\common\models\Employee $model) {
                            return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                        },
                        'format' => 'html'
                    ],*/

                    [
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
                    ],

                    [
                        'label' => 'Tasks Result for Period',
                        'value' => function(\common\models\Employee $model) use ($searchModel) {
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
                                return $cnt ? Html::a($cnt, ['lead-flow/index',
                                    'LeadFlowSearch[employee_id]' => $model->id,
                                    'LeadFlowSearch[status]' => \common\models\Lead::STATUS_PROCESSING,
                                    'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                    'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                                ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                            },
                            'format' => 'raw',
                    ],
                    /*[
                        'label' => 'Hold On',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_ON_HOLD], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_ON_HOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],*/
                    [
                        'label' => 'Booked',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_BOOKED], $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Sold',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_SOLD], $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_SOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Follow Up',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_FOLLOW_UP], $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_FOLLOW_UP,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Trash',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_TRASH], $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_TRASH,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ]

                ]
            ])
            ?>


        </div>
    </div>
    <?php Pjax::end(); ?>



</div>