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

$bundle = \frontend\assets\TimelineAsset2::register($this);
$this->title = 'Stats Employees';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="site-index">
    <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
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

        <div class="col-md-6">
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
                        $projectsValue = '';

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
            </table>
        </div>
    </div>

    <?php Pjax::begin(); ?>
    <div class="card card-default">
        <div class="card-header"><i class="fa fa-bar-chart"></i> Employees Stats <?=$searchModel->timeRange ? '(' . $searchModel->timeRange . ')' : ''?></div>
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
                            'locale' => [
                                    'format' => 'Y-m-d H:i'
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ]
                    ]);
                    ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-warning']) ?>
                    <?php //= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
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
                    /*[
                        'attribute' => 'username',
                        'value' => static function (\common\models\Employee $model) {
                            return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                        },
                        'format' => 'raw',
                        //'contentOptions' => ['title' => 'text-center'],
                        'options' => ['style' => 'width:180px'],
                    ],*/

                    'username:userName',

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
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_PROCESSING], null, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Hold On',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_ON_HOLD], \common\models\Lead::STATUS_PROCESSING, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_ON_HOLD,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Booked',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_BOOKED], null, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
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
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_SOLD], null, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_SOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Follow Up',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_FOLLOW_UP], \common\models\Lead::STATUS_PROCESSING, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_FOLLOW_UP,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->timeStart,
                                'LeadFlowSearch[created_date_to]' => $searchModel->timeEnd
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Trash',
                        'value' => static function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_TRASH], \common\models\Lead::STATUS_PROCESSING, $searchModel->timeStart, $searchModel->timeEnd);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[lf_owner_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_TRASH,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
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