<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$bundle = \frontend\assets\TimelineAsset::register($this);
$this->title = 'User Call Map';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/

$userId = Yii::$app->user->id;
?>

<div class="site-index">

    <?/*<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>*/?>

    <?php Pjax::begin(['id' => 'pjax-call-list']); ?>
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> User Call Map</div>
        <div class="panel-body">

            

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                    if ($model->deleted) {
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
                        'value' => function (\common\models\Employee $model) {
                            return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                        },
                        'format' => 'raw',
                        //'contentOptions' => ['title' => 'text-center'],
                        'options' => ['style' => 'width:180px'],
                    ],

                    [
                        //'attribute' => 'username',
                        'label' => 'Role',
                        'value' => function (\common\models\Employee $model) {
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
                        'value' => function (\common\models\Employee $model) {
                            return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                        },
                        'format' => 'html'
                    ],*/

                    [
                        'label' => 'User Groups',
                        'attribute' => 'user_group_id',
                        'value' => function (\common\models\Employee $model) {

                            $groups = $model->getUserGroupList();
                            $groupsValueArr = [];

                            foreach ($groups as $group) {
                                $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                            }

                            $groupsValue = implode(' ', $groupsValueArr);

                            return $groupsValue;
                        },
                        'format' => 'raw',
                        'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                    ],

                    [
                        'label' => 'Tasks Result for Period',
                        'value' => function(\common\models\Employee $model) use ($searchModel) {
                            return $model->getTaskStats($searchModel->datetime_start, $searchModel->datetime_end);
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
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_PROCESSING], null, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Hold On',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_ON_HOLD], \common\models\Lead::STATUS_PROCESSING, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_ON_HOLD,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Booked',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_BOOKED], null, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Sold',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_SOLD], null, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_SOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Follow Up',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_FOLLOW_UP], \common\models\Lead::STATUS_PROCESSING, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_FOLLOW_UP,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Processing -> Trash',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_TRASH], \common\models\Lead::STATUS_PROCESSING, $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_TRASH,
                                'LeadFlowSearch[lf_from_status_id]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ]


                    /*[
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'visibleButtons' => [
                            'update' => function (\common\models\Employee $model, $key, $index) {
                                return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                            },
                        ],

                    ],*/
                ]
            ])
            ?>


        </div>
    </div>
    <?php Pjax::end(); ?>



</div>