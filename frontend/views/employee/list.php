<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $employees [] */

use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = 'User List';
$this->params['breadcrumbs'][] = $this->title;



$isUM = Yii::$app->user->identity->canRole('userManager');
$isAdmin = Yii::$app->user->identity->canRoles(['admin', 'superadmin']);
$isSuperAdmin = Yii::$app->user->identity->canRole('superadmin');

if ($isAdmin || $isSuperAdmin) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

//Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()


?>
<div class="employee-index">
    <h1><?=$this->title?></h1>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add new User', 'create', [
            'class' => 'btn-success btn',
        ]) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => $template,
            'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                if ($model->isDeleted()) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width: 60px'],
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {projects} {groups} {switch}',
                    'visibleButtons' => [
                        /*'view' => function ($model, $key, $index) {
                            return User::hasPermission('viewOrder');
                        },*/
                        'update' => function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM) {
                            return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                        },
                        'projects' => function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM)  {
                            return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                        },
                        'groups' => function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM)  {
                            return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                        },
                        'switch' => function (\common\models\Employee $model, $key, $index)  use ($isAdmin, $isUM) {
                                return ($isAdmin && !$model->canRoles(['superadmin', 'admin']));
                        },
                    ],
                    'buttons' => [
                        'projects' => function ($url, \common\models\Employee $model, $key) {
                            return Html::a('<span class="fa fa-list"></span>', ['user-project-params/index', 'UserProjectParamsSearch[upp_user_id]' => $model->id], ['title' => 'Projects', 'target' => '_blank']);
                        },
                        'groups' => function ($url, \common\models\Employee $model, $key) {
                            return Html::a('<span class="fa fa-users"></span>', ['user-group-assign/index', 'UserGroupAssignSearch[ugs_user_id]' => $model->id], ['title' => 'User Groups', 'target' => '_blank']);
                        },
                        'switch' => function ($url, \common\models\Employee $model, $key) {
                            return Html::a('<span class="fa fa-sign-in"></span>', ['employee/switch', 'id' => $model->id], ['title' => 'switch User', 'data' => [
                                'confirm' => 'Are you sure you want to switch user?',
                                //'method' => 'get',
                            ],]);
                        },
                    ]
                ],

                [
                    'label' => 'Grav',
                    'value' => function (\common\models\Employee $model) {

                        if($model->email) {
                            $grav_url = "//www.gravatar.com/avatar/" . md5(mb_strtolower(trim($model->email))) . "?d=identicon&s=25";
                        } else {
                            $grav_url = '//www.gravatar.com/avatar/?d=identicon&s=25';
                        }

                        $icon = \yii\helpers\Html::img($grav_url, ['class' => 'img-circle img-thumbnail']);

                        return $icon;
                    },
                    'format' => 'raw'
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
                    //'label' => 'Online',
                    'attribute' => 'online',
                    //'filter' => false,
                    'filter' => [1 => 'Online', 2 => 'Offline'],
                    'value' => function (\common\models\Employee $model) {
                        return $model->isOnline() ? '<span class="label label-success">Online</span>' : '<span class="label label-danger">Offline</span>';
                    },
                    'format' => 'raw'
                ],

                [
                    'label' => 'Call Ready',
                    'filter' => false,
                    //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                    'value' => function (\common\models\Employee $model) {
                        return $model->isCallStatusReady() ? '<span class="label label-success">Ready</span>' : '<span class="label label-warning">Occupied</span>';
                    },
                    'format' => 'raw'
                ],

                /*[
                    'label' => 'Last Call Status',
                    'filter' => false,
                    //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                    'value' => function (\common\models\Employee $model) {

                        $call = \common\models\Call::find()->where(['c_created_user_id' => $model->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

                        return $call ? $call->c_call_status : '-';
                    },
                    'format' => 'raw'
                ],*/

                [
                    'label' => 'User Groups',
                    'attribute' => 'user_group_id',
                    'value' => function (\common\models\Employee $model) {

                        $groups = $model->getUserGroupList();
                        $groupsValueArr = [];

                        foreach ($groups as $group) {
                            $groupsValueArr[] = Html::tag('span', /*Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' .*/ Html::encode($group), ['class' => 'label label-info']);
                        }

                        $groupsValue = implode(' ', $groupsValueArr);

                        return $groupsValue;
                    },
                    'format' => 'raw',
                    'filter' => $isAdmin ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                ],



                /*[
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
                ],*/

                /*[
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
                ],*/
                [
                    'label' => 'Call type',
                    'attribute' => 'user_call_type_id',
                    'value' => function (\common\models\Employee $model) {
                        $call_type_id = '';
                        if($model->userProfile && is_numeric($model->userProfile->up_call_type_id)) {
                            $call_type_id = $model->userProfile->up_call_type_id;
                        }

                        return \common\models\UserProfile::CALL_TYPE_LIST[$call_type_id] ?? '-';
                    },
                    'format' => 'raw',
                    'filter' => \common\models\UserProfile::CALL_TYPE_LIST
                ],
                /*[
                    'label' => 'Sip',
                    'attribute' => 'user_sip',
                    'value' => function (\common\models\Employee $model) {
                        return ($model->userProfile->up_sip) ?? '';
                    },
                    'format' => 'raw'
                ],*/
                [
                    'label' => 'Projects Params',
                    'attribute' => 'user_params_project_id',
                    'value' => function (\common\models\Employee $model) {

                        $str = '<small><table class="table table-bordered">';

                        //$projects = $model->uppProjects;
                        $projectParams = $model->userProjectParams;
                        //$projectsValueArr = [];

                        if($projectParams) {
                            foreach ($projectParams as $projectParam) {
                                $str.='<tr>';
                                $str.='<td>'.Html::encode($projectParam->upp_project_id).'</td>';
                                $str.='<td>'.Html::encode($projectParam->uppProject->name).'</td>';
                                $str.='<td>'.Html::encode($projectParam->upp_tw_phone_number).'</td>';


                                //$str.='<td>'.Html::encode($projectParam->upp_tw_sip_id).'</td>';
                                //$str.='<td>'.Html::encode($model->userProfile->up_sip ?? null).'</td>';
                                //$projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-default']);
                                $str.='</tr>';
                            }
                        }

                        $str .= '</table></small>';

                        //$projectsValue = implode(' ', $projectsValueArr);

                        return $str; //$projectsValue;
                    },
                    'format' => 'raw',
                    'filter' => $projectList
                ],

                //'created_at:datetime',
                //'updated_at:datetime',
                // 'acl_rules_activated:boolean',

                [
                    'label' => 'IP filter',
                    'attribute' => 'acl_rules_activated',
                    'value' => function(\common\models\Employee $model) {
                        return $model->acl_rules_activated ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                    'filter' => [0 => 'No', 1 => 'Yes']
                    //'visible' => $isAdmin
                ],

                /*[
                    'label' => 'Base Amount',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return $model->userParams ? '$'.number_format($model->userParams->up_base_amount , 2) : '-';
                    },
                    //'format' => '',
                    'contentOptions' => ['class' => 'text-right'],
                    'visible' => $isAdmin
                ],*/

                /*[
                    'label' => 'Commission',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return $model->userParams ? $model->userParams->up_commission_percent. '%' : '-';
                    },
                    //'format' => 'html',
                    'contentOptions' => ['class' => 'text-right'],
                    'visible' => $isAdmin
                ],*/

                /*[
                    'label' => 'Bonus Active',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                    return $model->userParams ? $model->userParams->up_bonus_active ? 'Yes':'No' : '-';
                },
                //'format' => 'html',
                'contentOptions' => ['class' => 'text-right'],
                'visible' => $isAdmin
                ],*/

                /*[
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
                    'visible' => $isAdmin
                ],*/


                [
                    'label' => 'Bonuses',
                    'value' => function(\common\models\Employee $model) {
                        if($params = $model->userParams) {
                            $str = '<table class="table table-bordered" style="font-size:10px">';
                            $str .= '<tr><td>Bonus Active</td><td>'. ($params->up_bonus_active ? 'Yes':'No') . '</td></tr>';
                            $str .= '<tr><td>Commission</td><td>'. ($params->up_commission_percent ? $params->up_commission_percent . '%':'-') . '</td></tr>';
                            $str .= '<tr><td>Base Amount</td><td>' . ($params->up_base_amount ? number_format($params->up_base_amount, 2) : '-').'</td></tr>';
                            $str .= '</table>';
                        } else {
                            $str = '-';
                        }
                        return $str;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                    'options' => [
                        'style' => 'width:240px;'
                    ],
                    'visible' => $isAdmin
                ],

                [
                    'label' => 'Other params',
                    //'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        if($params = $model->userParams) {
                            $str = '<table class="table table-bordered" style="font-size:10px">';
                            $str .= '<tr><td>'.$params->getAttributeLabel('up_inbox_show_limit_leads').'</td><td>'.$params->up_inbox_show_limit_leads.'</td></tr>';
                            $str .= '<tr><td>'.$params->getAttributeLabel('up_default_take_limit_leads').'</td><td>'.$params->up_default_take_limit_leads.'</td></tr>';
                            $str .= '<tr><td>'.$params->getAttributeLabel('up_min_percent_for_take_leads').'</td><td>'.$params->up_min_percent_for_take_leads.'%</td></tr>';
                            $str .= '<tr><td>'.$params->getAttributeLabel('up_frequency_minutes').'</td><td>'.$params->up_frequency_minutes.'</td></tr>';
                            $str .= '<tr><td>'.$params->getAttributeLabel('up_call_expert_limit').'</td><td>'.$params->up_call_expert_limit.'</td></tr>';

                            $str .= '</table>';
                        } else {
                            $str = '-';
                        }
                        return $str;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                    'options' => [
                        'style' => 'width:240px;'
                    ],
                    'visible' => $isAdmin
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

            ]
        ])
        ?>
</div>
