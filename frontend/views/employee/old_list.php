<?php

use common\models\Employee;
use src\auth\Auth;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm \frontend\models\UserMultipleForm */
/* @var $employees [] */
/* @var array $multipleErrors */

use src\access\EmployeeProjectAccess;
use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap4\Modal;

$this->title = 'User List';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$isUM = $user->isUserManager();
$isAdmin = $user->isAdmin() || $user->isSuperAdmin();
$isOnlyAdmin = $user->isOnlyAdmin();
$isSuperAdmin = $user->isSuperAdmin();

if ($isAdmin || $isSuperAdmin) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}
$projectList = EmployeeProjectAccess::getProjects($user->id);

?>
<div class="employee-index">

    <?php $status = Yii::$app->params['settings']['two_factor_authentication_enable'] ?
        '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>' ?>

    <div class="alert btn-secondary alert-dismissible fade show" role="alert">
        Setting "Enable two factor authentication" is <?= $status ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <h1><?=$this->title?></h1>

    <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add new User', 'create', [
        'class' => 'btn-success btn',
    ]) ?>

    <?php Pjax::begin(['id' => 'user-pjax-list', 'timeout' => 8000, 'enablePushState' => true, 'enableReplaceState' => false, 'scrollTo' => 0]); ?>

    <?php

    echo $this->render('_search', [
        'model' => $searchModel,
    ]);
    ?>

    <?php /*= Html::label( Yii::t('frontend', 'Page size: '), 'pagesize', array( 'style' => 'margin-left:10px; margin-top:8px;' ) ) ?>
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

    <?php if ($multipleErrors || $multipleForm->getErrors()) : ?>
        <div class="card multiple-update-summary" style="margin-bottom: 10px;">
            <div class="card-header">
                <span class="pull-right clickable close-icon"><i class="fa fa-times"> </i></span>
                Errors:
            </div>
            <div class="card-body">
                <?php
                foreach ($multipleErrors as $userId => $multipleError) {
                    echo 'UserId: ' . $userId . ' <br>';
                    echo VarDumper::dumpAsString($multipleError) . ' <br><br>';
                }
                ?>
                <?= $multipleForm->getErrors() ? VarDumper::dumpAsString($multipleForm->getErrorSummary(true)) : '' ?>
            </div>
        </div>
        <?php
        $js = <<<JS
            $('.close-icon').on('click', function(){    
                $('.multiple-update-summary').slideUp();
            })
        JS;
        $this->registerJs($js);
        ?>
    <?php endif;?>

    <?php if ($user->isAdmin() || $user->isSupervision()) : ?>
        <p>
            <?php //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            <?php // \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning', 'data-toggle'=> 'modal', 'data-target'=>'#modalUpdate' ])?>

        <div class="btn-group">
            <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>
            
            <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <?= \yii\helpers\Html::a('<i class="fa fa-edit text-warning"></i> Multiple update', null, ['class' => 'dropdown-item btn-multiple-update', 'data-toggle' => 'modal', 'data-target' => '#modalUpdate' ])?>
                <div class="dropdown-divider"></div>
                <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids'])?>
            </div>
            <?php //endif; ?>
        </div>

        </p>

        <?php //= Html::textarea('selected-ids', '', ['rows' => 10, 'id' => "selected-ids", 'style' => 'display: none']); ?>
    <?php endif; ?>

    <?php /*php if($isAdmin):?>
            <p>
                <?= \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info','data-toggle' => 'modal','data-target' => '#modalUpdate'])?>
            </p>
        <?php endif;*/?>

    <?= \yii\grid\GridView::widget([
        'id' => 'user-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'layout'=>'{summary}'.Html::activeDropDownList($searchModel, 'perpage', [10 => 10, 30 => 30, 20 => 20, 50 => 50, 100 => 100],['id'=>'perpage'])."{items}<br/>{pager}",
        //'pjax' => false,
        //'layout' => $template,
        'rowOptions' => static function (\common\models\Employee $model, $index, $widget, $grid) {
            if ($model->isDeleted() || $model->isBlocked()) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
//            [
//                'class' => \kartik\grid\CheckboxColumn::class,
//                'name' => 'UserMultipleForm[user_list]',
//                //'pageSummary' => true,
//                'rowSelectedClass' => \kartik\grid\GridView::TYPE_INFO,
//            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox'
            ],
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'text-left', 'style' => 'width: 60px'],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{info} {update} {projects} {groups} {switch}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'info' => static function (\common\models\Employee $model, $key, $index) {
                        return Auth::can('/user/info');
                    },
                    'update' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'projects' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'groups' => static function (\common\models\Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'switch' => static function (\common\models\Employee $model, $key, $index) {
                        return !$model->isOnlyAdmin() && !$model->isSuperAdmin() && Auth::can('/employee/switch');
                    },
                ],
                'buttons' => [
                    'info' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-info-circle"></span>', ['user/info', 'id' => $model->id], ['title' => 'User info', 'target' => '_blank', 'class' => 'text-info']);
                    },
                    'projects' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-list"></span>', ['user-project-params/index', 'UserProjectParamsSearch[upp_user_id]' => $model->id], ['title' => 'Projects', 'target' => '_blank']);
                    },
                    'groups' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-users text-info"></span>', ['user-group-assign/index', 'UserGroupAssignSearch[ugs_user_id]' => $model->id], ['title' => 'User Groups', 'target' => '_blank']);
                    },
                    'switch' => static function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-sign-in text-warning"></span>', ['employee/switch', 'id' => $model->id], ['title' => 'switch User', 'data' => [
                            'confirm' => 'Are you sure you want to switch user?',
                            //'method' => 'get',
                        ],]);
                    },
                ]
            ],

            [
                'label' => 'Grav',
                'value' => static function (\common\models\Employee $model) {
                    $gravUrl = $model->getGravatarUrl(25);
                    return \yii\helpers\Html::img($gravUrl, ['class' => 'img-circle img-thumbnail']);
                },
                'format' => 'raw'
            ],
            'username:userName',
            'nickname',
            [
                'attribute' => 'roles',
                'label' => 'Role',
                'value' => static function (\common\models\Employee $model) {
                    $roles = $model->getRoles();
                    return $roles ? implode(', ', $roles) : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getAllRoles(Auth::user()),
                'contentOptions' => ['style' => 'width: 10%; white-space: pre-wrap']
            ],

            //'email:email',
            [
                'attribute' => 'status',
                'filter' => $searchModel::STATUS_LIST,
                'value' => static function (\common\models\Employee $model) {
                    return Yii::$app->formatter->asEmployeeStatusLabel($model->status);
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'online',
                'filter' => [1 => 'Online', 2 => 'Offline'],
                'value' => static function (\common\models\Employee $model) {
                    return $model->userOnline ? '<span class="label label-success">Online</span>' : '<span class="label label-danger">Offline</span>';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Call type',
                'attribute' => 'user_call_type_id',
                'value' => static function (\common\models\Employee $model) {
                    $call_type_id = '';
                    if ($model->userProfile && is_numeric($model->userProfile->up_call_type_id)) {
                        $call_type_id = $model->userProfile->up_call_type_id;
                    }

                    return \common\models\UserProfile::CALL_TYPE_LIST[$call_type_id] ?? '-';
                },
                'format' => 'raw',
                'filter' => \common\models\UserProfile::CALL_TYPE_LIST
            ],
            [
                'label' => 'Call Ready',
                'filter' => false,
                //'filter' => [1 => 'Online', $searchModel::STATUS_DELETED => 'Deleted'],
                'value' => static function (\common\models\Employee $model) {
                    return $model->isCallStatusReady() ? '<span class="label label-success">ON</span>' : '<span class="label label-warning">OFF</span>';
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
                        $groupsValueArr[] = '<div class="col-md-4">' . Html::tag('div', /*Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' .*/ Html::encode($group), ['class' => 'label label-info']) . '</div>';
                    }

                    $groupsValue = '<div class="row">' . implode(' ', $groupsValueArr) . '</div>';

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
                        $valueArr[] = '<div class="col-md-4">' . Html::tag('div', /*Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' .*/ Html::encode($item), ['class' => 'label label-default']) . '</div>';
                    }

                    $value = '<div class="row">' . implode(' ', $valueArr) . '</div>';

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

//            [
//                'label' => 'Projects Params',
//                'attribute' => 'user_params_project_id',
//                'value' => static function (\common\models\Employee $model) {
//
//                    $projects = $model->uppProjects;
//                    $projectsValueArr = [];
//
//                    if($projects) {
//                        foreach ($projects as $project) {
//                            $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-default']);
//                        }
//                    }
//
//                    $projectsValue = implode(' ', $projectsValueArr);
//
//                    return $projectsValue;
//                },
//                'format' => 'raw',
//                'filter' => $projectList
//            ],


            [
                'label' => 'Projects Params',
                'attribute' => 'user_params_project_id',
                'value' => static function (\common\models\Employee $model) {

                    $str = '<small><table class="table table-bordered">';

                    $projectParams = $model->userProjectParams;
                    if ($projectParams) {
                        foreach ($projectParams as $projectParam) {
                            $str .= '<tr>';
                            $str .= '<td>' . Html::encode($projectParam->uppProject->name) . '</td>';
                            $str .= '<td>' . Html::encode($projectParam->getPhone()) . '</td>';
                            $str .= '<td title="' . ($projectParam->uppDep ? $projectParam->uppDep->dep_name : '-') . '">' . Html::encode($projectParam->upp_dep_id) . '</td>';

                            if ($projectParam->upp_allow_general_line) {
                                $str .= '<td><span class="label label-success">Yes</span></td>';
                            } else {
                                $str .= '<td><span class="label label-danger">No</span></td>';
                            }

                            $str .= '</tr>';
                        }
                    }

                    $str .= '</table></small>';

                    return $str;
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
                'value' => static function (\common\models\Employee $model) {
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

//            [
//                'label' => 'Bonuses',
//                'value' => static function (\common\models\Employee $model) {
//                    if($params = $model->userParams) {
//                        $str = '<table class="table table-bordered" style="font-size:10px">';
//                        $str .= '<tr><td>Bonus Active</td><td>'. ($params->up_bonus_active ? 'Yes':'No') . '</td></tr>';
//                        $str .= '<tr><td>Commission</td><td>'. ($params->up_commission_percent ? $params->up_commission_percent . '%':'-') . '</td></tr>';
//                        $str .= '<tr><td>Base Amount</td><td>' . ($params->up_base_amount ? number_format($params->up_base_amount, 2) : '-').'</td></tr>';
//                        $str .= '</table>';
//                    } else {
//                        $str = '-';
//                    }
//                    return $str;
//                },
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-left'],
//                'options' => [
//                    'style' => 'width:240px;'
//                ],
//                'visible' => $isAdmin
//            ],

//            [
//                'label' => 'Other params',
//                //'attribute' => 'created_at',
//                'value' => static function(\common\models\Employee $model) {
//                    if($params = $model->userParams) {
//                        $str = '<table class="table table-bordered" style="font-size:10px">';
//                        $str .= '<tr><td>'.$params->getAttributeLabel('up_inbox_show_limit_leads').'</td><td>'.$params->up_inbox_show_limit_leads.'</td></tr>';
//                        $str .= '<tr><td>'.$params->getAttributeLabel('up_default_take_limit_leads').'</td><td>'.$params->up_default_take_limit_leads.'</td></tr>';
//                        $str .= '<tr><td>'.$params->getAttributeLabel('up_min_percent_for_take_leads').'</td><td>'.$params->up_min_percent_for_take_leads.'%</td></tr>';
//                        $str .= '<tr><td>'.$params->getAttributeLabel('up_frequency_minutes').'</td><td>'.$params->up_frequency_minutes.'</td></tr>';
//                        $str .= '<tr><td>'.$params->getAttributeLabel('up_call_expert_limit').'</td><td>'.$params->up_call_expert_limit.'</td></tr>';
//
//                        $str .= '</table>';
//                    } else {
//                        $str = '-';
//                    }
//                    return $str;
//                },
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-left'],
//                'options' => [
//                    'style' => 'width:240px;'
//                ],
//                'visible' => $isAdmin
//            ],

            /*[
                'attribute' => 'created_at',
                'value' => function(\common\models\Employee $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created_at));
                },
                'format' => 'raw',
            ],*/
//            [
//                'label' => 'Work Experience',
//                'attribute' => 'experienceMonth',
//                'value' => static function (Employee $model) {
//                    return $model->userProfile ? $model->userProfile->getExperienceMonth() : 0;
//                }
//            ],
//            [
//                'attribute' => 'joinDate',
//                'value' => static function (Employee $model) {
//                    return $model->userProfile ? $model->userProfile->up_join_date : null;
//                },
//                'filter' => DatePicker::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'joinDate',
//                    'clientOptions' => [
//                        'autoclose' => true,
//                        'format' => 'yyyy-mm-dd',
//                    ],
//                    'options' => [
//                        'autocomplete' => 'off',
//                        'placeholder' =>'Choose Date',
//                        'style' => 'width: 150px'
//                    ],
//                ]),
//            ],
//            [
//                'label' => '2FA enable',
//                'value' => static function (\common\models\Employee $model) {
//                    return ($model->userProfile && $model->userProfile->up_2fa_enable) ?
//                        '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>';
//                },
//                'format' => 'raw'
//            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated_at'
            ],

            /*[
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
            ],*/

        ]
    ])
?>

    <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'user-list-update-form', /*'action' => ['employee/list'],*/ 'method' => 'post', 'options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

    <?php if ($isAdmin) : ?>
    <p>
        <?php //= \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning','data-toggle' => 'modal','data-target' => '#modalUpdate'])?>
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
                            <?= $form->field($multipleForm, 'userDepartments')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->getDepartments(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user Departments', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            <?= $form->field($multipleForm, 'userRoles')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->getRoles(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user roles', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            <?= $form->field($multipleForm, 'status')->dropDownList([$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'workStart')->widget(
                                \kartik\time\TimePicker::class,
                                [
                                'readonly' => true,
                                'pluginOptions' => [
                                    'defaultTime' => false,
                                    'showSeconds' => false,
                                    'showMeridian' => false,
                                ]]
                            ) ?>
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
                            <?= $form->field($multipleForm, 'userClientChatChanels')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->getClientChatChanels(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select Client Chat Chanels', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($multipleForm, 'minPercentForTakeLeads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'frequencyMinutes')->input('number', ['step' => 1, 'max' => 1000, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'baseAmount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 1000]) ?>
                            <?= $form->field($multipleForm, 'commissionPercent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            <?= $form->field($multipleForm, 'up_call_expert_limit')->input('number', ['min' => -1, 'max' => 1000]) ?>
                            <?= $form->field($multipleForm, 'autoRedial')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'kpiEnable')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                            <?= $form->field($multipleForm, 'leaderBoardEnabled')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
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

        <?php
        $selectAllUrl = \yii\helpers\Url::to(array_merge(['employee/list'], Yii::$app->getRequest()->getQueryParams(), ['act' => 'select-all']));
        ?>
        <script>
            var selectAllUrl = '<?=$selectAllUrl?>';
        </script>

        <?php Pjax::end(); ?>

        <?php
        $js = <<<JS

   // $(document).ready(function () {
        $(document).on('click', '.btn-multiple-update', function() {
            
            let arrIds = [];
            if (sessionStorage.selectedUsers) {
                let data = jQuery.parseJSON( sessionStorage.selectedUsers );
                arrIds = Object.values(data);
                /*if (data) {
                     $.each( data, function( key, value ) {
                        arrIds.push(value);
                     });
                }*/
                
                $('#user_list_json').val(JSON.stringify(arrIds));
                //alert(arrIds.join(', '));
            }
            
            //$('#user_list_json').attr('value', sessionStorage.selectedUsers);
            // let keys = $('#user-list-grid').yiiGridView('getSelectedRows');
            //alert(JSON.stringify(keys));
            //alert(JSON.stringify(arrIds));
            // $('#user_list_json').attr('value', JSON.stringify(keys));
        });
    //}); 

    $(document).on('pjax:start', function() {
        $("#modalUpdate .close").click();
    });
    
    $('#user-pjax-list').on('pjax:end', function() {
        refreshUserSelectedState();
    });
    
    
    $('body').on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if ($(this).hasClass('checked')) {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.selectedUsers = '{}';
            //sessionStorage.removeItem('selectedUsers');
        } else {
            btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');
            
            $.ajax({
             type: 'post',
             dataType: 'json',
             //data: {},
             url: selectAllUrl,
             success: function (data) {
                console.info(data);
                let cnt = Object.keys(data).length
                if (data) {
                    let jsonData = JSON.stringify(data);
                    sessionStorage.selectedUsers = jsonData;
                    btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
                    
                    $('.select-on-check-all').prop('checked', true); //.trigger('click');
                    $("input[name='selection[]']").prop('checked', true);
                } else {
                    btn.html('<span class="fa fa-square-o"></span> Check All');
                }
             },
             error: function (error) {
                    btn.html('<span class="fa fa-error text-danger"></span> Error ...');
                    console.error(error);
                    alert('Request Error');
                 }
             });
        }
    });

    
    function refreshUserSelectedState() {
         if (sessionStorage.selectedUsers) {
            let data = jQuery.parseJSON( sessionStorage.selectedUsers );
            let btn = $('#btn-check-all');
            
            let cnt = Object.keys(data).length;
            if (cnt > 0) {
                $.each( data, function( key, value ) {
                  $("input[name='selection[]'][value=" + value + "]").prop('checked', true);
                });
                btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
                
            } else {
                btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
                $('.select-on-check-all').prop('checked', false);
            }
        }
    }
    
    $('body').on('click', '.btn-show-checked-ids', function(e) {
       let data = [];
       if (sessionStorage.selectedUsers) {
            data = jQuery.parseJSON( sessionStorage.selectedUsers );
            let arrIds = [];
            if (data) {
                arrIds = Object.values(data);
                 // $.each( data, function( key, value ) {
                 //    arrIds.push(value);
                 // });
            }
            alert('User IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
       } else {
           alert('sessionStorage.selectedUsers = null');
       }
    });
    
    $('body').on('change', '.select-on-check-all', function(e) {
        let checked = $('#user-list-grid').yiiGridView('getSelectedRows');
        let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
        let data = [];
        if (sessionStorage.selectedUsers) {
            data = jQuery.parseJSON( sessionStorage.selectedUsers );
        }
       
        $.each( checked, function( key, value ) {
            if (typeof data[value] === 'undefined') {
              data[value] = value;
            }
        });
        
       $.each( unchecked, function( key, value ) {
          if (typeof data[value] !== 'undefined') {
                delete(data[value]);
          }
        });
       
       sessionStorage.selectedUsers = JSON.stringify(data);
       refreshUserSelectedState();
    });
    
    refreshUserSelectedState();

    /*$(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });*/

JS;

        $this->registerJs($js, \yii\web\View::POS_READY);
        ?>
    </div>
</div>
