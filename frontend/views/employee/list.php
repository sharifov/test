<?php

use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm \frontend\models\UserMultipleForm */
/* @var $employees [] */

use sales\access\EmployeeProjectAccess;
use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap4\Modal;

$this->title = 'User List';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;

$isUM = $user->canRole('userManager');
$isAdmin = $user->canRoles(['admin', 'superadmin']);
$isSuperAdmin = $user->canRole('superadmin');

if ($isAdmin || $isSuperAdmin) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}
$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

//Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : $user->getUserGroupList()

?>
<div class="employee-index">
    <h1><?=$this->title?></h1>

    <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add new User', 'create', [
        'class' => 'btn-success btn',
    ]) ?>

    <?php Pjax::begin(['id' => 'user-pjax-list', 'timeout' => 8000, 'enablePushState' => true, 'enableReplaceState' => false]); //['id' => 'user-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php

    echo $this->render('_search', [
        'model' => $searchModel,
    ]);
    ?>

    <?/*= Html::label( Yii::t('frontend', 'Page size: '), 'pagesize', array( 'style' => 'margin-left:10px; margin-top:8px;' ) ) ?>
    <?= Html::dropDownList(
        'pagesize',
        ( isset($_GET['pagesize']) ? $_GET['pagesize'] : 20 ),  // set the default value for the dropdown list
        // set the key and value for the drop down list
        array(
            20 => 20,
            50 => 50,
            100 => 100),
        // add the HTML attritutes for the dropdown list
        // I add pagesize as an id of dropdown list. later on, I will add into the Gridview widget.
        // so when the form is submitted, I can get the $_POST value in InvoiceSearch model.
        array(
            'id' => 'pagesize',
            'style' => 'margin-left:5px; margin-top:8px;'
        )
    )*/
    ?>

    <?php if($user->canRoles(['admin', 'supervision'])) : ?>
        <p>
            <?//= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            <?= \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning', 'data-toggle'=> 'modal', 'data-target'=>'#modalUpdate' ])?>
        </p>
    <?php endif; ?>

    <?/*php if($isAdmin):?>
            <p>
                <?= \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info','data-toggle' => 'modal','data-target' => '#modalUpdate'])?>
            </p>
        <?php endif;*/?>

    <?= GridView::widget([
        'id' => 'user-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'layout'=>'{summary}'.Html::activeDropDownList($searchModel, 'perpage', [10 => 10, 30 => 30, 20 => 20, 50 => 50, 100 => 100],['id'=>'perpage'])."{items}<br/>{pager}",
        'pjax' => false,
        //'layout' => $template,
        'rowOptions' => static function (\common\models\Employee $model, $index, $widget, $grid) {
            if ($model->isDeleted()) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            [
                'class' => \kartik\grid\CheckboxColumn::class,
                'name' => 'UserMultipleForm[user_list]',
                //'pageSummary' => true,
                'rowSelectedClass' => \kartik\grid\GridView::TYPE_INFO,
            ],
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'text-left', 'style' => 'width: 60px'],
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{update} {projects} {groups} {switch}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                    },
                    'projects' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM)  {
                        return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                    },
                    'groups' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM)  {
                        return ($isAdmin || !$model->canRoles(['superadmin', 'admin']));
                    },
                    'switch' => static function (\common\models\Employee $model, $key, $index)  use ($isAdmin, $isUM) {
                        return ($isAdmin && !$model->canRoles(['superadmin', 'admin']));
                    },
                ],
                'buttons' => [
                    'projects' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-list"></span>', ['user-project-params/index', 'UserProjectParamsSearch[upp_user_id]' => $model->id], ['title' => 'Projects', 'target' => '_blank']);
                    },
                    'groups' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-users"></span>', ['user-group-assign/index', 'UserGroupAssignSearch[ugs_user_id]' => $model->id], ['title' => 'User Groups', 'target' => '_blank']);
                    },
                    'switch' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-sign-in"></span>', ['employee/switch', 'id' => $model->id], ['title' => 'switch User', 'data' => [
                            'confirm' => 'Are you sure you want to switch user?',
                            //'method' => 'get',
                        ],]);
                    },
                ]
            ],

            [
                'label' => 'Grav',
                'value' => static function (\common\models\Employee $model) {

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
            'username:userName',
            [
                'attribute' => 'roles',
                'label' => 'Role',
                'value' => static function (\common\models\Employee $model) {
                    $roles = $model->getRoles();
                    return $roles ? implode(', ', $roles) : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getAllRoles()
            ],

            'email:email',
            [
                'attribute' => 'status',
                'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                'value' => static function (\common\models\Employee $model) {
                    return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                },
                'format' => 'raw'
            ],

            [
                //'label' => 'Online',
                'attribute' => 'online',
                //'filter' => false,
                'filter' => [1 => 'Online', 2 => 'Offline'],
                'value' => static function (\common\models\Employee $model) {
                    return $model->isOnline() ? '<span class="label label-success">Online</span>' : '<span class="label label-danger">Offline</span>';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Call Ready',
                'filter' => false,
                //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                'value' => static function (\common\models\Employee $model) {
                    return $model->isCallStatusReady() ? '<span class="label label-success">Ready</span>' : '<span class="label label-warning">Occupied</span>';
                },
                'format' => 'raw'
            ],

            /*[
                'label' => 'Last Call Status',
                'filter' => false,
                //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                'value' => static function (\common\models\Employee $model) {

                    $call = \common\models\Call::find()->where(['c_created_user_id' => $model->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

                    return $call ? $call->c_call_status : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'label' => 'User Groups',
                'attribute' => 'user_group_id',
                'value' => static function (\common\models\Employee $model) {

                    $groups = $model->getUserGroupList();
                    $groupsValueArr = [];

                    foreach ($groups as $group) {
                        $groupsValueArr[] = '<div class="col-md-4">'.Html::tag('div', /*Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' .*/ Html::encode($group), ['class' => 'label label-info']).'</div>';
                    }

                    $groupsValue = '<div class="row">'.implode(' ', $groupsValueArr).'</div>';

                    return $groupsValue;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left', 'style' => 'width: 280px'],
                'filter' => $isAdmin ? \common\models\UserGroup::getList() : $user->getUserGroupList()
            ],

            [
                'label' => 'User Departments',
                'attribute' => 'user_department_id',
                'value' => static function (\common\models\Employee $model) {

                    $list = $model->getUserDepartmentList();
                    $valueArr = [];

                    foreach ($list as $item) {
                        $valueArr[] = '<div class="col-md-4">'.Html::tag('div', /*Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' .*/ Html::encode($item), ['class' => 'label label-default']).'</div>';
                    }

                    $value = '<div class="row">'.implode(' ', $valueArr).'</div>';

                    return $value;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left', 'style' => 'width: 280px'],
                'filter' => \common\models\Department::getList()
            ],


            /*[
                'label' => 'Projects access',
                'attribute' => 'user_project_id',
                'value' => static function (\common\models\Employee $model) {

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
                'value' => static function (\common\models\Employee $model) {

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
                'value' => static function (\common\models\Employee $model) {
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
                'value' => static function (\common\models\Employee $model) {
                    return ($model->userProfile->up_sip) ?? '';
                },
                'format' => 'raw'
            ],*/
            [
                'label' => 'Projects Params',
                'attribute' => 'user_params_project_id',
                'value' => static function (\common\models\Employee $model) {

                    $str = '<small><table class="table table-bordered">';

                    //$projects = $model->uppProjects;
                    $projectParams = $model->userProjectParams;
                    //$projectsValueArr = [];

                    if($projectParams) {
                        foreach ($projectParams as $projectParam) {
                            $str .= '<tr>';
//                                $str.='<td>'.Html::encode($projectParam->upp_project_id).'</td>';
                            $str .= '<td>'.Html::encode($projectParam->uppProject->name).'</td>';
                            $str .= '<td>'.Html::encode($projectParam->upp_tw_phone_number).'</td>';
                            $str .= '<td title="' . ($projectParam->uppDep ? $projectParam->uppDep->dep_name : '-') . '">' . Html::encode($projectParam->upp_dep_id) . '</td>';

                            if ($projectParam->upp_allow_general_line) {
                                $str .= '<td><span class="label label-success">Yes</span></td>';
                            } else {
                                $str .= '<td><span class="label label-danger">No</span></td>';
                            }

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
                'value' => static function (\common\models\Employee $model) {
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
                'value' => static function(\common\models\Employee $model) {
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
                'value' => static function (\common\models\Employee $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated_at));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

        ]
    ])
    ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'user-list-update-form', /*'action' => ['employee/list'],*/ 'method' => 'post', 'options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

    <?php if($isAdmin) : ?>
    <p>
        <?= \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning','data-toggle' => 'modal','data-target' => '#modalUpdate'])?>
    </p>

    <?= $form->errorSummary($multipleForm) ?>

    <?php

    Modal::begin([
        'title' => 'Multiple update selected Users',
        'id' => 'modalUpdate'
    ]);
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($multipleForm, 'userDepartment')->dropDownList(\common\models\Department::getList(), ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'userRole')->dropDownList(\common\models\Employee::getAllRoles(), ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'status')->dropDownList([$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'workStart')->widget(
                                \kartik\time\TimePicker::class, [
                                'readonly' => true,
                                'pluginOptions' => [
                                    'defaultTime' => false,
                                    'showSeconds' => false,
                                    'showMeridian' => false,
                                ]]) ?>
                            <?= $form->field($multipleForm, 'workMinutes')->input('number', ['step' => 10, 'min' => 0])?>
                            <?=
                            $form->field($multipleForm, 'timeZone')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\Employee::timezoneList(true),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]);
                            ?>
                            <?= $form->field($multipleForm, 'inboxShowLimitLeads')->input('number', ['step' => 1, 'min' => 0, 'max' => 500]) ?>
                            <?= $form->field($multipleForm, 'defaultTakeLimitLeads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($multipleForm, 'minPercentForTakeLeads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'frequencyMinutes')->input('number', ['step' => 1, 'max' => 1000, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'baseAmount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 1000]) ?>
                            <?= $form->field($multipleForm, 'commissionPercent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'up_call_expert_limit')->input('number', ['min' => -1, 'max' => 1000]) ?>
                            <?= $form->field($multipleForm, 'autoRedial')->dropDownList([1 =>'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'kpiEnable')->dropDownList([1 =>'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'leaderBoardEnabled')->dropDownList([1 =>'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'user_list_json')->hiddenInput(['id' => 'user_list_json'])->label(false) ?>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <?= Html::submitButton('<i class="fa fa-check-square"></i> Update selected Users', ['id' => 'btn-submit-multiple-update', 'class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php Modal::end(); ?>
        <?php endif; ?>

        <?php \yii\bootstrap\ActiveForm::end(); ?>

        <?php Pjax::end(); ?>

        <?php
        $js = <<<JS

    $(document).ready(function () {
        $(document).on('click', '#btn-submit-multiple-update', function() {
            var keys = $('#user-list-grid').yiiGridView('getSelectedRows');
            $('#user_list_json').attr('value', JSON.stringify(keys));
        });
    }); 

    $(document).on('pjax:start', function() {
        $("#modalUpdate .close").click();
    });

    /*$(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });*/

JS;

        $this->registerJs($js, \yii\web\View::POS_READY);
        ?>
    </div>
</div>
