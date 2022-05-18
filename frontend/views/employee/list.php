<?php

use common\models\Employee;
use kartik\select2\Select2;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\user\src\update\MultipleUpdateForm;
use src\auth\Auth;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm MultipleUpdateForm */
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

    <?php if (Auth::can('employee/multipleUpdate')) : ?>
        <p>
            <?php //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            <?php // \yii\helpers\Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-warning', 'data-toggle'=> 'modal', 'data-target'=>'#modalUpdate' ])?>

        <div class="btn-group">
            <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

            <?php // if (\webvimark\modules\UserManagement\models\User::canRoute('/email-layout/delete-selected')): ?>
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
        'rowOptions' => static function (Employee $model, $index, $widget, $grid) {
            if ($model->isDeleted() || $model->isBlocked()) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox',
                'visible' => Auth::can('employee/multipleUpdate'),
            ],
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'text-left', 'style' => 'width: 60px'],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{info} {update} {projects} {groups} {switch} {shiftCalendar}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'info' => static function (Employee $model, $key, $index) {
                        return Auth::can('/user/info');
                    },
                    'update' => static function (Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'projects' => static function (Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'groups' => static function (Employee $model, $key, $index) use ($isAdmin, $isUM) {
                        return (
                            $isAdmin
                            || ($isUM && (!$model->isOnlyAdmin() && !$model->isSuperAdmin()))
                            || !($model->isAdmin() || $model->isSuperAdmin())
                        );
                    },
                    'switch' => static function (Employee $model, $key, $index) {
                        return !$model->isOnlyAdmin() && !$model->isSuperAdmin() && Auth::can('/employee/switch');
                    },
                    'shiftCalendar' => static function (Employee $model, $key, $index) {
                        /** @abac ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to action user-shift-calendar */
                        return \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_ACCESS
                        );
                    },
                ],
                'buttons' => [
                    'info' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-info-circle"></span>',
                            ['user/info', 'id' => $model->id],
                            ['title' => 'User info', 'target' => '_blank', 'class' => 'text-info']
                        );
                    },
                    'projects' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-list"></span>',
                            ['user-project-params/index', 'UserProjectParamsSearch[upp_user_id]' => $model->id],
                            ['title' => 'Projects', 'target' => '_blank']
                        );
                    },
                    'groups' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-users text-info"></span>',
                            ['user-group-assign/index', 'UserGroupAssignSearch[ugs_user_id]' => $model->id],
                            ['title' => 'User Groups', 'target' => '_blank']
                        );
                    },
                    'switch' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-sign-in text-warning"></span>',
                            ['employee/switch', 'id' => $model->id],
                            ['title' => 'switch User', 'data' => [
                            'confirm' => 'Are you sure you want to switch user?',
                            //'method' => 'get',
                            ],
                            ]
                        );
                    },
                    'shiftCalendar' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-calendar"></span>',
                            ['shift-schedule/user', 'id' => $model->id],
                            ['title' => 'User Shift Calendar', 'target' => '_blank']
                        );
                    },
                ]
            ],

            [
                'label' => 'Grav',
                'value' => static function (Employee $model) {
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
                'value' => static function (Employee $model) {
                    $roles = $model->getRoles();
                    return $roles ? implode(', ', $roles) : '-';
                },
                'format' => 'raw',
                'filter' => Employee::getAllRoles(Auth::user()),
                'contentOptions' => ['style' => 'width: 10%; white-space: pre-wrap']
            ],

            [
                'attribute' => 'status',
                'filter' => $searchModel::STATUS_LIST,
                'value' => static function (Employee $model) {
                    return Yii::$app->formatter->asEmployeeStatusLabel($model->status);
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'online',
                'filter' => [1 => 'Online', 2 => 'Offline'],
                'value' => static function (Employee $model) {
                    return $model->userOnline ? '<span class="label label-success">Online</span>' : '<span class="label label-danger">Offline</span>';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Call type',
                'attribute' => 'user_call_type_id',
                'value' => static function (Employee $model) {
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
                'value' => static function (Employee $model) {
                    return $model->isCallStatusReady() ? '<span class="label label-success">ON</span>' : '<span class="label label-warning">OFF</span>';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'User Groups',
                'attribute' => 'user_group_id',
                'value' => static function (Employee $model) {

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
                'value' => static function (Employee $model) {

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

            [
                'label' => 'Projects Params',
                'attribute' => 'user_params_project_id',
                'value' => static function (Employee $model) {

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
                'filter' => EmployeeProjectAccess::getProjects($user->id)
            ],

            [
                'label' => 'IP filter',
                'attribute' => 'acl_rules_activated',
                'value' => static function (Employee $model) {
                    return $model->acl_rules_activated ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'filter' => [0 => 'No', 1 => 'Yes']
                //'visible' => $isAdmin
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated_at'
            ],
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
            'id' => 'modalUpdate',
            'size' => 'modal-md'
        ]);
        ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($multipleForm, 'user_departments')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->availableList->getDepartments(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user Departments', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            <?= $form->field($multipleForm, 'user_departments_action')->dropDownList($multipleForm::DEPARTMENTS_ACTION_LIST) ?>

                            <?= $form->field($multipleForm, 'form_roles')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->availableList->getRoles(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user roles', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            <?= $form->field($multipleForm, 'form_roles_action')->dropDownList($multipleForm::ROLES_ACTION_LIST) ?>

                            <?= $form->field($multipleForm, 'user_groups', ['options' => ['class' => 'form-group']])->widget(Select2::class, [
                                'data' => $multipleForm->availableList->getUserGroups(),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select user groups', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            <?= $form->field($multipleForm, 'user_groups_action')->dropDownList(MultipleUpdateForm::GROUPS_ACTION_LIST) ?>

                            <?= $form->field($multipleForm, 'client_chat_user_channel')->widget(\kartik\select2\Select2::class, [
                                'data' => $multipleForm->availableList->getClientChatUserChannels(),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select Client Chat Channels', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_min_percent_for_take_leads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_frequency_minutes')->input('number', ['step' => 1, 'max' => 1000, 'min' => 0]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_base_amount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 1000]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_commission_percent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_call_expert_limit')->input('number', [ 'step' => 1, 'min' => -1, 'max' => 1000,]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_auto_redial')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_kpi_enable')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_leaderboard_enabled')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_inbox_show_limit_leads')->input('number', ['step' => 1, 'min' => 0, 'max' => 500]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_default_take_limit_leads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_work_start_tm')->widget(
                                        \kartik\time\TimePicker::class,
                                        [
                                            'readonly' => true,
                                            'pluginOptions' => [
                                                'defaultTime' => false,
                                                'showSeconds' => false,
                                                'showMeridian' => false,
                                            ]]
                                    ) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($multipleForm, 'up_work_minutes')->input('number', ['step' => 10, 'min' => 0])?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->field($multipleForm, 'up_timezone')->widget(\kartik\select2\Select2::class, [
                                        'data' => $multipleForm->availableList->getTimezones(),
                                        'size' => \kartik\select2\Select2::SMALL,
                                        'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
                                        'pluginOptions' => ['allowClear' => true],
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->field($multipleForm, 'status')->dropDownList($multipleForm->availableList->getStatuses(), ['prompt' => '']) ?>
                                </div>
                            </div>
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
