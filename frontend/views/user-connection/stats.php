<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $employees [] */

use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = 'User Stats';
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
    <div class="panel-heading">Users Stats</div>
    <div class="panel-body">
        <div class="row mb-20">
            <?/*<div class="col-md-6">
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Create new User', 'create', [
                    'class' => 'btn-success btn',
                ]) ?>
            </div>*/?>
        </div>
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

                //'email:email',
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

                [
                    'label' => 'Last Call Status',
                    'filter' => false,
                    //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                    'value' => function (\common\models\Employee $model) {

                        $call = \common\models\Call::find()->where(['c_created_user_id' => $model->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

                        return $call ? $call->c_call_status : '-';
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
                                //$str.='<td>'.Html::encode($model->userProfile->up_sip ?? '').'</td>';
                                //$str.='<td>'.Html::encode($projectParam->upp_tw_sip_id).'</td>';
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


                /*[
                    'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created_at));
                    },
                    'format' => 'raw',
                ],*/




            ]
        ])
        ?>
    </div>
</div>
