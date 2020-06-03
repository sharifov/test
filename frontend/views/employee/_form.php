<?php

use common\components\grid\DateTimeColumn;
use common\models\UserProductType;
use frontend\models\UserFailedLogin;
use modules\product\src\entities\productType\ProductType;
use sales\auth\Auth;
use yii\web\View;
use yii\grid\ActionColumn;

/**
 * @var $this \yii\web\View
 * @var $modelUserParams \common\models\UserParams
 * @var $modelProfile \common\models\UserProfile
 */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataUserProductType yii\data\ActiveDataProvider */
/* @var $model common\models\Employee */
/* @var $userVoiceMailProvider \yii\data\ActiveDataProvider */
/* @var UserFailedLogin[] $lastFailedLoginAttempts */

use sales\access\EmployeeProjectAccess;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;
use common\models\EmployeeAcl;
use yii\widgets\MaskedInput;

$data = [];
$dataProjects = [];

/** @var Employee $user */
$user = Yii::$app->user->identity;

if($model->isNewRecord) {
    $this->title = 'Create new User';
} else {
    $this->title ='Update user: ' . $model->username.' (ID:  '.$model->id.')';
}

$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;


if ($user->isAdmin() || $user->isSuperAdmin() || $user->isUserManager()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

$projectList = EmployeeProjectAccess::getProjects($user->id);

?>


<div class="col-sm-5">
    <?php $form = ActiveForm::begin([
        'successCssClass' => '',
        'id' => sprintf('%s-ID', $model->formName())
    ]) ?>
            <div class="well">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'username')->textInput(['autocomplete' => "new-user"]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => "new-password"]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'full_name')->textInput() ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'email')->input('email') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <?php if($model->isNewRecord || $user->isAdmin() || $user->isSuperAdmin() || $user->isSupervision() || $user->isUserManager()): ?>
                            <?php //= $form->field($model, 'roles')->dropDownList($model::getAllRoles(), ['prompt' => '']) ?>

                            <?php
                                echo $form->field($model, 'form_roles')->widget(\kartik\select2\Select2::class, [
                                    'data' => $model::getAllRoles(),
                                    'size' => \kartik\select2\Select2::SMALL,
                                    'options' => ['placeholder' => 'Select user roles', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ]);
                            ?>

                        <?php else: ?>
                            <div>
                            <label class="control-label">Role</label>:
                                <b><?=implode(', ', $model->getRoles())?></b>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!$model->isNewRecord) : ?>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'deleted', ['template' => '{label}{input}'])->checkbox() ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <?php if($model->isNewRecord || $user->isAdmin() || $user->isSuperAdmin() || $user->isSupervision() || $user->isUserManager() || $user->isAgent()):

                            if($user->isAdmin() || $user->isSuperAdmin() || $user->isUserManager()) {
                                $data = \common\models\UserGroup::getList();
                                $dataProjects = \common\models\Project::getList();
                            }

                            if($user->isSupervision()) {
                                $data = $user->getUserGroupList();
                                $dataProjects = \yii\helpers\ArrayHelper::map($user->projects, 'id', 'name');
                                //\yii\helpers\VarDumper::dump($dataProjects, 10, true);                             //exit;
                            }


                            ?>

                            <?php
                                echo $form->field($model, 'user_groups')->widget(\kartik\select2\Select2::class, [
                                    'data' => $data,
                                    'size' => \kartik\select2\Select2::SMALL,
                                    'options' => ['placeholder' => 'Select user groups', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ]);
                            ?>


                            <?php
                            echo $form->field($model, 'user_projects')->widget(\kartik\select2\Select2::class, [
                                'data' => $dataProjects,
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user projects', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]);
                            ?>

                            <?php
                                echo $form->field($model, 'user_departments')->widget(\kartik\select2\Select2::class, [
                                    'data' => \common\models\Department::getList(),
                                    'size' => \kartik\select2\Select2::SMALL,
                                    'options' => ['placeholder' => 'Select departments', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ]);
                            ?>

                        <?php else: ?>

                            <div class="col-md-12">
                                <label class="control-label">User Groups</label>:
                                <?php
                                    $groupsValue = '';
                                    if( $groupsModel =  $model->ugsGroups) {
                                        $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');

                                        $groupsValueArr = [];
                                        foreach ($groups as $group) {
                                            $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                                        }
                                        $groupsValue = implode(' ', $groupsValueArr);
                                    }
                                    echo $groupsValue;
                                ?>
                            </div>

                            <div class="col-md-12">
                                <label class="control-label">Projects access</label>:
                                <?php
                                    $projectsValueArr = [];

                                    if($projects = $model->projects) {
                                        foreach ($projects as $project) {
                                            $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-info']);
                                        }
                                    }

                                    $projectsValue = implode(' ', $projectsValueArr);
                                    echo $projectsValue;
                                ?>
                            </div>

                        <?php endif; ?>
                    </div>

                </div>

                <?php if($user->isAdmin() || $user->isSuperAdmin() || $user->isSupervision()): ?>

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $form->errorSummary($modelUserParams) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_base_amount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 1000]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_commission_percent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_bonus_active')->checkbox() ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_leaderboard_enabled')->checkbox() ?>
                        </div>
                    </div>




                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_work_start_tm')->widget(
                                \kartik\time\TimePicker::class, [
                                    'pluginOptions' => [
                                        'showSeconds' => false,
                                        'showMeridian' => false,
                                ]])?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelUserParams, 'up_work_minutes')->input('number', ['step' => 10, 'min' => 0])?>
                        </div>
                        <div class="col-md-6">
                            <?php //= $form->field($modelUserParams, 'up_timezone')->dropDownList(Employee::timezoneList(),['prompt' =>'-'])?>
                            <?php
                            echo $form->field($modelUserParams, 'up_timezone')->widget(\kartik\select2\Select2::class, [
                                'data' => Employee::timezoneList(true),
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]);
                            ?>
                        </div>
                    </div>

                    <?php if(!Yii::$app->user->identity->canRole('supervision')): ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($modelUserParams, 'up_inbox_show_limit_leads')->input('number', ['step' => 1, 'min' => 0, 'max' => 500]) ?>
                                <?= $form->field($modelUserParams, 'up_call_expert_limit')->input('number', ['step' => 1, 'min' => -1, 'max' => 1000]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($modelUserParams, 'up_default_take_limit_leads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($modelUserParams, 'up_min_percent_for_take_leads')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($modelUserParams, 'up_frequency_minutes')->input('number', ['step' => 1, 'max' => 1000, 'min' => 0]) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
            <?php
            if (!$model->isNewRecord) : ?>
                <div class="well">
                    <div class="form-group">
                        <?= $form->field($model, 'acl_rules_activated', [
                            'template' => '{input}'
                        ])->checkbox() ?>
                        <span>&nbsp;</span>
                        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add Extra Rule', null, [
                            'class' => 'btn btn-success btn-xs',
                            'id' => 'acl-rule-id',
                        ]) ?>
                        <?php
                        $aclModel = new EmployeeAcl();
                        $aclModel->employee_id = $model->id;
                        $idForm = sprintf('%s-ID', $aclModel->formName());
                        $idMaskIP = Html::getInputId($aclModel, 'mask');

                        $js = <<<JS
    $('#acl-rule-id').on('click', function() {
        $(this).addClass('d-none');
        $('#$idForm').removeClass('d-none');
    });

    $('#close-btn').on('click', function() {
        $('#acl-rule-id').removeClass('d-none');
        $('#$idForm').addClass('d-none');
    });

    $('#submit-btn').on('click', function() {
        $.post($(this).data('url'), $('#$idForm input').serialize(),function( data ) {
            if (data.success) {
                $('#employee-acl-rule').html(data.body);
                $('#close-btn').trigger('click');
                $('#$idMaskIP').val('');
            } else {
                $('#$idMaskIP').parent().addClass('has-error');
            }
        });
    });
JS;
                        $this->registerJs($js);
                        ?>
                        <div class="form-group d-none" id="<?= $idForm ?>">
                            <?= $form->field($aclModel, 'employee_id', [
                                'options' => [
                                    'tag' => false
                                ],
                                'template' => '{input}'
                            ])->hiddenInput() ?>
                            <?= $form->field($aclModel, 'mask', [
                                'options' => [
                                    'class' => 'form-group'
                                ],
                                'template' => '{label}: {input}',
                                'enableClientValidation' => false
                            ])->textInput(['maxlength' => true])
//                                ->widget(MaskedInput::class, [
//                                'clientOptions' => [
//                                    'alias' => 'ip',
//                                ],
//                            ])
                            ?>
                            <span>&nbsp;</span>
                            <?= $form->field($aclModel, 'description', [
                                'options' => [
                                    'class' => 'form-group'
                                ],
                                'template' => '{label}: {input}',
                                'enableClientValidation' => false
                            ])->textInput() ?>
                            <span>&nbsp;</span>
                            <?= Html::button('Save', [
                                'class' => 'btn-success btn',
                                'id' => 'submit-btn',
                                'data-url' => Yii::$app->urlManager->createUrl(['employee/acl-rule', 'id' => 0])
                            ]) ?>
                            <?= Html::button('Close', [
                                'class' => 'btn-danger btn',
                                'id' => 'close-btn'
                            ]) ?>
                        </div>
                    </div>
                    <div class="grid-view" id="employee-acl-rule" style="padding-top: 10px;">
                        <?= $this->render('partial/_aclList', [
                            'models' => $model->employeeAcl
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <h4>Profile Settings</h4>

            <div class="row">
                <div class="col-md-3">
                    <?php if ($modelProfile->up_join_date === null): $modelProfile->up_join_date = date('Y-m-d'); endif; ?>
                    <?= $form->field($modelProfile, 'up_join_date')->widget(\dosamigos\datepicker\DatePicker::class, [
						'clientOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd',
						],
						'options' => [
							'autocomplete' => 'off',
							'placeholder' =>'Choose Date',
						],
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelProfile, 'up_call_type_id')->dropDownList(\common\models\UserProfile::CALL_TYPE_LIST) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelProfile, 'up_sip')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelProfile, 'up_telegram')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($modelProfile, 'up_telegram_enable')->checkbox() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelProfile, 'up_skill')->dropDownList(\common\models\UserProfile::SKILL_TYPE_LIST, ['prompt' => '---']) ?>
                    <?= $form->field($modelProfile, 'up_auto_redial')->checkbox() ?>
                    <?= $form->field($modelProfile, 'up_kpi_enable')->checkbox() ?>
                    <?= $form->field($modelProfile, 'up_show_in_contact_list')->checkbox() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelProfile, 'up_2fa_enable')->checkbox() ?>
                    <?= $form->field($modelProfile, 'up_2fa_secret')->textInput(['maxlength' => true])->label('2fa secret. Clean for reset') ?>
                </div>
            </div>

            <div class="form-group text-center">
                <?= Html::submitButton(($model->isNewRecord ? '<i class="fa fa-plus"></i> Create User' : '<i class="fa fa-save"></i> Update & Save User data'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
            </div>
    <?php ActiveForm::end() ?>
</div>


<div class="col-sm-7">

    <?php if (!$model->isNewRecord) : ?>

        <div class="user-project-params-index">

            <h4>Project Params</h4>

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-upp']); ?>
            <p>
                <?php //= Html::a('Create User Project Params', ['user-project-params/create'], ['class' => 'btn btn-success']) ?>

                <?php echo Html::a('<i class="glyphicon glyphicon-plus"></i> Create Project Params',null,
                    [
                        'class' => 'btn btn-success btn-xs act-create-upp',
                        'title' => 'Create Project Params',
                        //'data-toggle'=>'modal',
                        //'data-target'=>'#activity-modal',
                        'data-user_id' => $model->id,
                        'data-pjax' => '0',
                    ]
                );
                ?>

            </p>


            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],


                    [
                        'attribute' => 'upp_project_id',
                        'value' => function(\common\models\UserProjectParams $model) {
                            return $model->uppProject ? ''.$model->uppProject->name.'' : '-';
                        },
                        'filter' => $projectList
                        //'format' => 'raw'
                        //'contentOptions' => ['class' => 'text-right']
                    ],

                    [
                        'attribute' => 'upp_dep_id',
                        'value' => function(\common\models\UserProjectParams $model) {
                            return $model->uppDep ? ''.$model->uppDep->dep_name.'' : '-';
                        },
                    ],

                    //'upp_user_id',
                    //'upp_project_id',
                    'upp_email:email',
                    [
                        'class' => \common\components\grid\EmailSelect2Column::class,
                        'attribute' => 'upp_email_list_id',
                        'relation' => 'emailList',
                    ],
                    //'upp_phone_number',
                    'upp_tw_phone_number',
                    [
                        'class' => \common\components\grid\PhoneSelect2Column::class,
                        'attribute' => 'upp_phone_list_id',
                        'relation' => 'phoneList',
                    ],
                    [
                        'attribute' => 'upp_allow_general_line',
                        'format' => 'raw',
                        'value' => function(\common\models\UserProjectParams $model) {
                            if ($model->upp_allow_general_line) {
                                return '<span class="label label-success">Yes</span>';
                            }
                            return '<span class="label label-danger">No</span>';
                        }
                    ],
                    //'upp_tw_sip_id',

                    /*[
                        'label' => 'Action',
                        'value' => function(\common\models\UserProjectParams $model) {
                            return Html::a('<i class="glyphicon glyphicon-edit"></i> Update',
                                 ['user-project-params/update','upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id, 'redirect' => 'employee/update?id='.$model->upp_user_id],
                                [
                                    'class' => 'btn btn-xs btn-warning',
                                    'title' => 'Update Params',
                                    'data-toggle'=>'modal',
                                    'data-target'=>'#modal-dialog',
                                    'data-id' => $model->upp_user_id . '_' . $model->upp_project_id,
                                    //'data-pjax' => '0',
                                ]
                            );

                        },
                        'format' => 'raw'
                    ],*/

                    //'upp_created_dt',
                    //'upp_updated_dt',
                    /*[
                        'attribute' => 'upp_updated_dt',
                        'value' => function(\common\models\UserProjectParams $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->upp_updated_dt));
                        },
                        'format' => 'raw',
                    ],*/



                    [
                        'class' => ActionColumn::class,
                        'template' => '{update} {delete}',
                        'controller' => 'user-project-params',
                        //'headerOptions' => ['width' => '20%', 'class' => '',],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>','#', [
                                    'class' => 'act-update-upp',
                                    'title' => 'Update Project params',
                                    //'data-toggle' => 'modal',
                                    //'data-target' => '#activity-modal',
                                    'data-id' => $key,
                                    'data-pjax' => '0',
                                ]);
                            },
                        ],

                    ],
                ],
            ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>

        <div class="user-voice-mail">
            <h4>User Voice Mail</h4>
			<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-voice-mail']); ?>

            <p>
				<?php echo Html::a('<i class="glyphicon glyphicon-plus"></i> Add Voice Mail',null,
					[
						'class' => 'btn btn-success btn-xs add-voice-mail',
						'title' => 'Add Voice Mail',
						'data-user_id' => $model->id,
						'data-pjax' => '0',
					]
				)?>
            </p>

			<?= \yii\grid\GridView::widget([
				'dataProvider' => $userVoiceMailProvider,
				'columns' => [
					'uvm_name',
					'uvm_say_language',
					'uvm_record_enable:booleanByLabel',
					'uvm_max_recording_time',
					'uvm_transcribe_enable:booleanByLabel',
					'uvm_enabled:booleanByLabel',
					'uvm_created_dt:byUserDateTime',
					'uvm_updated_dt:byUserDateTime',
					[
						'class' => ActionColumn::class,
						'template' => '{view} {update} {delete}',
						'controller' => 'user-voice-mail',
						'buttons' => [
							'update' => static function ($key) {
								return Html::a('<span class="glyphicon glyphicon-edit"></span>','#', [
                                    'class' => 'update-user-voice-mail',
                                    'title' => 'Update Voice Mail',
                                    'data-id' => $key,
                                    'data-pjax' => '0',
                                ]);
							},
							'delete' => static function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                    'class' => 'delete-user-voice-mail',
                                    'title' => 'Delete Voice Mail',
                                    'data-pjax' => '0'
                                ]);
							},
						],
					],
				],
			]); ?>

			<?php \yii\widgets\Pjax::end(); ?>
        </div>

        <?php if (Auth::can('user-product-type/list')) :?>
            <div class="user-product-type">
                <h4>Product Type</h4>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-product-type']); ?>

                <?php if (Auth::can('user-product-type/create')) :?>
                    <p>
                        <?php echo Html::a('<i class="glyphicon glyphicon-plus"></i> Add Product Type',null,
                            [
                                'class' => 'btn btn-success btn-xs add-product-type',
                                'title' => 'Add Product Type',
                                'data-user_id' => $model->id,
                                'data-user_name' => $model->username,
                                'data-pjax' => '0',
                            ]
                        )?>
                    </p>
                <?php endif ?>

                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataUserProductType,
                    'columns' => [
                        [
                            'attribute' => 'upt_product_type_id',
                            'value' => function(UserProductType $model) {
                                return $model->productType->pt_name;
                            },
                        ],
                        'upt_commission_percent',
                        'upt_product_enabled:booleanByLabel',
                        [
                            'class' => ActionColumn::class,
                            'template' => '{update} {delete}',
                            'controller' => 'user-product-type',
                            'buttons' => [
                                'update' => static function ($key) {
                                    if (Auth::can('user-product-type/update')) {
                                        $updateButton = Html::a('<span class="glyphicon glyphicon-edit"></span>','#', [
                                            'class' => 'update-product-type',
                                            'title' => 'Update Product Type',
                                            'data-id' => $key,
                                            'data-pjax' => '0',
                                        ]);
                                    } else {
                                        $updateButton = Html::tag('span', '', [
                                            'class' => 'glyphicon glyphicon-edit text-secondary',
                                            'title' => 'No access'
                                        ]);
                                    }
                                    return $updateButton;
                                },
                                'delete' => static function ($url) {
                                    if (Auth::can('user-product-type/delete')) {
                                        $deleteButton = Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, []);
                                    } else {
                                        $deleteButton = Html::tag('span', '', [
                                            'class' => 'glyphicon glyphicon-trash text-secondary',
                                            'title' => 'No access'
                                        ]);
                                    }
                                    return $deleteButton;
                                },
                            ],
                        ],
                    ],
                ]); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>
        <?php endif ?>


        <?php if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) :?>

            <div class="user-failed-login">
                <h4>User Failed Login</h4>

                <?php if ($model->isBlocked()) :?>
                    <p>
                        <?php echo Html::a('<i class="glyphicon glyphicon-remove-circle"></i> User Blocked',null,
                            [
                                'class' => 'btn btn-warning btn-xs unblock-user',
                                'title' => 'Click to unblock user',
                                'data-user_id' => $model->id,
                                'data-pjax' => '0',
                            ]
                        )?>
                    </p>
                <?php endif ?>

                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-user-failed']); ?>

                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $lastFailedLoginAttempts,
                    'rowOptions' => function (UserFailedLogin $UserFailedLogin, $index, $widget, $grid) {
                        if ($UserFailedLogin->ufl_created_dt > $UserFailedLogin->limitDateTime) {
                            return ['class' => 'danger'];
                        }
                    },
                    'columns' => [
                        'ufl_ip',
                        'ufl_ua',
                        'ufl_session_id',
                        'ufl_created_dt:byUserDateTime',
                    ],
                ]); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>



        <?php endif ?>

        <?php /*
        <div class="card card-default">
            <div class="panel-heading collapsing-heading">
                <?= Html::a('Seller Contact Info <i class="collapsing-heading__arrow"></i>', '#seller-contact-info', [
                    'data-toggle' => 'collapse',
                    'class' => 'collapsing-heading__collapse-link'
                ]) ?>
            </div>
            <div class="panel-body panel-collapse collapse in" id="seller-contact-info">
                <?= $this->render('partial/_sellerContactInfo', [
                    'model' => $model
                ]) ?>
            </div>
        </div>
        */ ?>


    <?php endif; ?>
    <?php /*= $this->render('partial/_activities', [
        'model' => $model
    ])*/ ?>

    <?php /*
    if (!$model->isNewRecord && !$model->isAdmin()) {
        echo $this->render('partial/_permissions', [
            'model' => $model,
            'isProfile' => $isProfile
        ]);
    }*/
    ?>
</div>


<?php
$js = <<<JS

     $(document).on('click', '.unblock-user', function(e) {
         e.preventDefault();
        
         if(!confirm('Are you sure un-block this user?')) {
            return true;
         }
         
         let objBtn = $(this);
         let htmlInner = objBtn.html();
             
         $.ajax({
            type: 'post',
            url: '/user-failed-login/set-active-ajax',
            dataType: 'json',
            data: {id:objBtn.data('user_id')},                
            beforeSend: function () {                    
                objBtn.html('<span class="spinner-border spinner-border-sm"></span>');
                objBtn.prop('disabled', true);    
            },
            success: function (dataResponse) {            
                objBtn.prop('disabled', false);
                objBtn.html(htmlInner); 
                  
                if (dataResponse.status === 1) {                        
                    objBtn.hide(); 
                    new PNotify({
                        title: "Success",
                        type: "success",
                        text: dataResponse.message,
                        hide: true
                    });                      
                } else {                        
                    new PNotify({
                        title: "Error:",
                        type: "error",
                        text: dataResponse.message,
                        hide: true
                    });
                }                       
            },
            error: function () {
                objBtn.prop('disabled', false);
                objBtn.html(htmlInner); 
            }
         });  
    });

    $('#modal-df').on('hidden.bs.modal', function () {
        $.pjax.reload({container:'#pjax-grid-upp', 'async': false});
        $.pjax.reload({container:'#pjax-grid-voice-mail', 'async': false});
        $.pjax.reload({container: "#pjax-grid-product-type", 'async': false});
        
        /*new PNotify({
            title: 'Params successfully updated',
            text: 'User project Parameters have been saved successfully.',
            type: 'success'
        });*/
    });
      

    /*$("#update-app-pjax").on("pjax:end", function() {
        $.pjax.reload({container:'#pjax-grid-upp'});
        $('#activity-modal').modal('hide');
    });*/


    $(document).on('click', '.act-update-upp', function(e) {
        e.preventDefault();
        //alert(123);
        
        let modal = $('#modal-df');
        
        $.get('/user-project-params/update-ajax',
            {
                data: $(this).closest('tr').data('key')
            },
            function (data) {
                
                modal.find('.modal-title').html('Update Project params');
                modal.find('.modal-body').html(data);
                modal.modal();
                
                //$('#activity-modal .modal-content').html(data);
                //$('#activity-modal').modal();
                
                

            }
        );
    });


    $(document).on('click', '.act-create-upp', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        $.get('/user-project-params/create-ajax', {user_id: $(this).data('user_id')},
            function (data) {
            
                modal.find('.modal-title').html('Create Project params');
                modal.find('.modal-body').html(data);
                modal.modal();
            
                //$('#activity-modal .modal-content').html(data);
                //$('#activity-modal').modal();
            }
        );
    });
    
    $(document).on('click', '.add-product-type', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        let userName = $(this).data('user_name');
        $.get('/user-product-type/create-ajax', {user_id: $(this).data('user_id')},
            function (data) {
                modal.find('.modal-title').html('Add Product Type for ' + userName);
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.update-product-type', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-product-type/update-ajax', {data : $(this).closest('tr').data('key')},
            function (data) {
                modal.find('.modal-title').html('Update Product Type');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.update-user-voice-mail', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-voice-mail/ajax-update', {id: $(this).closest('tr').data('key')},
            function (data) {
                modal.find('.modal-title').html('Update Voice Mail');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.delete-user-voice-mail', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        if (confirm('Confirm deletion...'))
        $.get('/user-voice-mail/ajax-delete', {id: $(this).closest('tr').data('key')},
            function (data) {
                if (!data.error) {
                    $.pjax.reload({container:'#pjax-grid-voice-mail', 'async': false});
                } else {
                    createNotify('Error', data.message, 'error');
                }
            }
        );
    });
    
    $(document).on('click', '.add-voice-mail', function (e) { 
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-voice-mail/ajax-create', {uid: $(this).data('user_id')},
            function (data) {
                modal.find('.modal-title').html('Add Voice Mail');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
JS;
$this->registerJs($js);
