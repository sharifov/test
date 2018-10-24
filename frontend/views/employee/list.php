<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $employees [] */

use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = 'User List';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

//Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()


?>
<div class="panel panel-default">
    <div class="panel-heading">Employees</div>
    <div class="panel-body">
        <div class="row mb-20">
            <div class="col-md-6">
                <?= Html::a('Create', 'update', [
                    'class' => 'btn-success btn',
                ]) ?>
            </div>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => $template,
            'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                if ($model->deleted) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width: 60px'],
                ],
                [
                    'attribute' => 'username',
                    'value' => function (\common\models\Employee $model) {
                        return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                    },
                    'format' => 'raw'
                ],

                [
                    //'attribute' => 'username',
                    'label' => 'Role',
                    'value' => function (\common\models\Employee $model) {
                        $roles = $model->getRoles();
                        return $roles ? implode(', ', $roles) : '-';
                    },
                    'format' => 'raw'
                ],

                'email:email',
                [
                    'attribute' => 'status',
                    'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                    'value' => function (\common\models\Employee $model) {
                        return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                    },
                    'format' => 'raw'
                ],

                [
                    'label' => 'User Groups',
                    'attribute' => 'user_group_id',
                    'value' => function (\common\models\Employee $model) {

                        $groups = $model->getUserGroupList();
                        $groupsValueArr = [];

                        foreach ($groups as $group) {
                            $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-info']);
                        }

                        $groupsValue = implode(' ', $groupsValueArr);

                        return $groupsValue;
                    },
                    'format' => 'raw',
                    'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                ],



                [
                    'label' => 'Projects access',
                    'attribute' => 'user_project_id',
                    'value' => function (\common\models\Employee $model) {

                        $projects = $model->projects;
                        $projectsValueArr = [];

                        if($projects) {
                            foreach ($projects as $project) {
                                $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-info']);
                            }
                        }

                        $projectsValue = implode(' ', $projectsValueArr);

                        return $projectsValue;
                    },
                    'format' => 'raw',
                    'filter' => $projectList
                ],

                [
                    'label' => 'Projects Params',
                    'attribute' => 'user_params_project_id',
                    'value' => function (\common\models\Employee $model) {

                        $projects = $model->uppProjects;
                        $projectsValueArr = [];

                        if($projects) {
                            foreach ($projects as $project) {
                                $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-default']);
                            }
                        }

                        $projectsValue = implode(' ', $projectsValueArr);

                        return $projectsValue;
                    },
                    'format' => 'raw',
                    'filter' => $projectList
                ],

                //'created_at:datetime',
                //'updated_at:datetime',
                // 'acl_rules_activated:boolean',

                [
                    //'label' => 'Base Amount',
                    'attribute' => 'acl_rules_activated',
                    'value' => function(\common\models\Employee $model) {
                        return $model->acl_rules_activated ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                    'filter' => [0 => 'No', 1 => 'Yes']
                    //'visible' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)
                ],

                [
                    'label' => 'Base Amount',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return $model->userParams ? '$'.number_format($model->userParams->up_base_amount , 2) : '-';
                    },
                    //'format' => '',
                    'contentOptions' => ['class' => 'text-right'],
                    'visible' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)
                ],

                [
                    'label' => 'Commission',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return $model->userParams ? $model->userParams->up_commission_percent. '%' : '-';
                    },
                    //'format' => 'html',
                    'contentOptions' => ['class' => 'text-right'],
                    'visible' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)
                ],

                [
                    'label' => 'Bonus Active',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                    return $model->userParams ? $model->userParams->up_bonus_active ? 'Yes':'No' : '-';
                },
                //'format' => 'html',
                'contentOptions' => ['class' => 'text-right'],
                'visible' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)
                ],

                [
                    'label' => 'Bonus Profit',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        $bonusProfit = $model->getProfitBonuses();
                        if(empty($bonusProfit)){
                            return Html::a('- ', ['profit-bonus/create/?user_id='.$model->id], ['data-pjax' => 0, 'target' => '_blank']);
                        }
                        $return = [];
                        foreach ($bonusProfit as $profit => $bonus){
                            $return[] = '>= '.$profit."&nbsp;->&nbsp;".$bonus;
                        }
                        return Html::a(implode('<br/>', $return), ['profit-bonus/index/?user_id='.$model->id], ['data-pjax' => 0, 'target' => '_blank']);
                    },
                    'format' => 'html',
                    'contentOptions' => ['class' => 'text-left'],
                    'options' => [
                        'style' => 'width:120px'
                    ],
                    'visible' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)
                ],


                /*[
                    'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created_at));
                    },
                    'format' => 'raw',
                ],*/


                [
                    'attribute' => 'updated_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated_at));
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {projects} {groups}',
                    'visibleButtons' => [
                        /*'view' => function ($model, $key, $index) {
                            return User::hasPermission('viewOrder');
                        },*/
                        'update' => function (\common\models\Employee $model, $key, $index) {
                            return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                        },
                        'projects' => function (\common\models\Employee $model, $key, $index) {
                            return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                        },
                        'groups' => function (\common\models\Employee $model, $key, $index) {
                            return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                        },
                    ],
                    'buttons' => [
                        'projects' => function ($url, \common\models\Employee $model, $key) {
                            return Html::a('<span class="fa fa-list"></span>', ['user-project-params/index', 'UserProjectParamsSearch[upp_user_id]' => $model->id], ['title' => 'Projects', 'target' => '_blank']);
                        },
                        'groups' => function ($url, \common\models\Employee $model, $key) {
                            return Html::a('<span class="fa fa-users"></span>', ['user-group-assign/index', 'UserGroupAssignSearch[ugs_user_id]' => $model->id], ['title' => 'User Groups', 'target' => '_blank']);
                        },
                    ]
                ],
            ]
        ])
        ?>
    </div>
</div>
