<?php

use common\models\EmployeeAcl;
use common\models\UserProductType;
use common\models\UserProjectParams;
use frontend\models\UserFailedLogin;
use kartik\select2\Select2;
use modules\user\src\update\UpdateForm;
use src\auth\Auth;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $userProjectParamsDataProvider yii\data\ActiveDataProvider */
/* @var $dataUserProductTypeDataProvider yii\data\ActiveDataProvider */
/* @var $form UpdateForm */
/* @var $userVoiceMailProvider \yii\data\ActiveDataProvider */
/* @var $dataLastFailedLoginDataProvider \yii\data\ActiveDataProvider */

$this->title = 'Update user: ' . $form->targetUser->username . ' (ID:  ' . $form->targetUser->id . ')';

$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;

?>

    <div class="row">
        <div class="col-md-12">
            <?= Html::errorSummary($form) ?>
        </div>
    </div>

    <div class="col-sm-5">
        <?php $activeForm = ActiveForm::begin([
            'successCssClass' => '',
            'id' => sprintf('%s-ID', $form->formName()),
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validationUrl' => Url::to(['employee/employee-validation', 'id' => (int)$form->targetUser->id]),
        ]) ?>
        <div class="well">

            <?php if ($form->targetUser->isBlocked()) : ?>
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-warning"></i> This user is <strong>Blocked</strong>!
                </div>
            <?php endif ?>

            <?php if ($form->targetUser->isDeleted()) : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-warning"></i> This user is <strong>Deleted</strong>!
                </div>
            <?php endif ?>

            <div class="row">
                <?php if ($form->fieldAccess->canViewUsername() || $form->fieldAccess->canEditUsername()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm->field($form, 'username', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->textInput([
                            'autocomplete' => "new-user",
                            'readonly' => !$form->fieldAccess->canEditUsername()
                        ]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewEmail() || $form->fieldAccess->canEditEmail()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'email', ['options' => ['class' => 'form-group']])
                            ->input('email', ['readonly' => !$form->fieldAccess->canEditEmail()])
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">

                <?php if ($form->fieldAccess->canViewFullName() || $form->fieldAccess->canEditFullName()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'full_name', ['options' => ['class' => 'form-group']])
                            ->textInput(['readonly' => !$form->fieldAccess->canEditFullName()])
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewPassword() || $form->fieldAccess->canEditPassword()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm->field($form, 'password', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->passwordInput([
                            'autocomplete' => 'new-password',
                            'readonly' => !$form->fieldAccess->canEditPassword()
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <?php if ($form->fieldAccess->canViewNickname() || $form->fieldAccess->canEditNickname()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'nickname', ['options' => ['class' => 'form-group']])
                            ->textInput(['readonly' => !$form->fieldAccess->canEditNickname()])
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">

                <?php if ($form->fieldAccess->canViewRoles() || $form->fieldAccess->canEditRoles()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'form_roles', ['options' => ['class' => 'form-group']])
                            ->widget(Select2::class, [
                                'data' => $form->availableList->getRoles(),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select user roles', 'multiple' => true],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'disabled' => !$form->fieldAccess->canEditRoles(),
                                ],
                            ])
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewStatus() || $form->fieldAccess->canEditStatus()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'status', ['options' => ['class' => 'form-group']])
                            ->dropDownList(
                                $form->availableList->getStatuses(),
                                ['disabled' => !$form->fieldAccess->canEditStatus()]
                            )
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-sm-12">

                    <?php if ($form->fieldAccess->canViewUserGroups() || $form->fieldAccess->canEditUserGroups()) : ?>
                        <?= $activeForm->field(
                            $form,
                            'user_groups',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getUserGroups(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select user groups', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditUserGroups()
                            ],
                            ]); ?>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canViewProjects() || $form->fieldAccess->canEditProjects()) : ?>
                        <?= $activeForm->field(
                            $form,
                            'user_projects',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getProjects(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select user projects', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditProjects()
                            ],
                            ]) ?>

                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canViewDepartments() || $form->fieldAccess->canEditDepartments()) : ?>
                        <?= $activeForm->field(
                            $form,
                            'user_departments',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getDepartments(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select departments', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditDepartments()
                            ],
                            ]); ?>

                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canViewClientChatUserChannels() || $form->fieldAccess->canEditClientChatUserChannels()) : ?>
                        <?= $activeForm->field(
                            $form,
                            'client_chat_user_channel',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getClientChatUserChannels(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Client Chat Chanel', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditClientChatUserChannels()
                            ],
                            ]); ?>

                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canViewUserShiftAssign() || $form->fieldAccess->canEditUserShiftAssign()) : ?>
                        <?= $activeForm->field(
                            $form,
                            'user_shift_assigns',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getUserShiftAssign(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditClientChatUserChannels()
                            ],
                            ]); ?>

                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <?php if ($form->fieldAccess->canViewWorkStartTime() || $form->fieldAccess->canEditWorkStartTime()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field(
                            $form,
                            'up_work_start_tm',
                            ['options' => ['class' => 'form-group']]
                        )->widget(
                            \kartik\time\TimePicker::class,
                            [
                                'pluginOptions' => [
                                    'showSeconds' => false,
                                    'showMeridian' => false,
                                ],
                                'disabled' => !$form->fieldAccess->canEditWorkStartTime()
                                ]
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewWorkMinutes() || $form->fieldAccess->canEditWorkMinutes()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_work_minutes', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input(
                            'number',
                            ['step' => 10, 'min' => 0, 'readonly' => !$form->fieldAccess->canEditWorkMinutes()]
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewTimeZone() || $form->fieldAccess->canEditTimeZone()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm->field(
                            $form,
                            'up_timezone',
                            ['options' => ['class' => 'form-group']]
                        )->widget(Select2::class, [
                            'data' => $form->availableList->getTimezones(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'disabled' => !$form->fieldAccess->canEditTimeZone()
                            ],
                            ]); ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="row">
                <?php if ($form->fieldAccess->canViewBaseAmount() || $form->fieldAccess->canEditBaseAmount()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_base_amount', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 0.01,
                            'min' => 0,
                            'max' => 1000,
                            'readonly' => !$form->fieldAccess->canEditBaseAmount()
                        ]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewCommissionPercent() || $form->fieldAccess->canEditCommissionPercent()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_commission_percent', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'max' => 100,
                            'min' => 0,
                            'readonly' => !$form->fieldAccess->canEditCommissionPercent()
                        ]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewBonusActive() || $form->fieldAccess->canEditBonusActive()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_bonus_active', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->checkbox(['disabled' => !$form->fieldAccess->canEditBonusActive()]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($form->fieldAccess->canViewLeaderboardEnabled() || $form->fieldAccess->canEditLeaderboardEnabled()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_leaderboard_enabled', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->checkbox(['disabled' => !$form->fieldAccess->canEditLeaderboardEnabled()]) ?>
                    </div>
                <?php endif; ?>

            </div>
            <hr>
            <div class="row">
                <?php if ($form->fieldAccess->canViewInboxShowLimitLeads() || $form->fieldAccess->canEditInboxShowLimitLeads()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_inbox_show_limit_leads', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'min' => 0,
                            'max' => 500,
                            'readonly' => !$form->fieldAccess->canEditInboxShowLimitLeads()
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canViewDefaultTakeLimitLeads() || $form->fieldAccess->canEditDefaultTakeLimitLeads()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_default_take_limit_leads', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'max' => 100,
                            'min' => 0,
                            'readonly' => !$form->fieldAccess->canEditDefaultTakeLimitLeads()
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canViewMinPercentForTakeLeads() || $form->fieldAccess->canEditMinPercentForTakeLeads()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_min_percent_for_take_leads', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'max' => 100,
                            'min' => 0,
                            'readonly' => !$form->fieldAccess->canEditMinPercentForTakeLeads()
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canViewFrequencyMinutes() || $form->fieldAccess->canEditFrequencyMinutes()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_frequency_minutes', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'max' => 1000,
                            'min' => 0,
                            'readonly' => !$form->fieldAccess->canEditFrequencyMinutes()
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>


            <div class="row">

                <?php if ($form->fieldAccess->canViewCallExpertLimit() || $form->fieldAccess->canEditCallExpertLimit()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_call_expert_limit', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'min' => -1,
                            'max' => 1000,
                            'readonly' => !$form->fieldAccess->canEditCallExpertLimit()
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canViewCallUserLevel() || $form->fieldAccess->canEditCallUserLevel()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_call_user_level', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->input('number', [
                            'step' => 1,
                            'min' => -128,
                            'max' => 127,
                            'readonly' => !$form->fieldAccess->canEditCallUserLevel()
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>


        <div class="well">
            <div class="form-group">
                <?= $activeForm->field($form, 'acl_rules_activated', [
                    'template' => '{input}'
                ])->checkbox() ?>
                <span>&nbsp;</span>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add Extra Rule', null, [
                    'class' => 'btn btn-success btn-xs',
                    'id' => 'acl-rule-id',
                ]) ?>
                <?php
                $aclModel = new EmployeeAcl();
                $aclModel->employee_id = $form->targetUser->id;
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
                    <?= $activeForm->field($aclModel, 'employee_id', [
                        'options' => [
                            'tag' => false
                        ],
                        'template' => '{input}'
                    ])->hiddenInput() ?>
                    <?= $activeForm->field($aclModel, 'mask', [
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
                    <?= $activeForm->field($aclModel, 'description', [
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
                <?= $this->render('../partial/_aclList', [
                    'models' => $form->targetUser->employeeAcl
                ]) ?>
            </div>
        </div>

        <h5>Profile Settings</h5>
        <div class="well">
            <div class="form-group">
                <div class="row">
                    <?php if ($form->fieldAccess->canViewJoinDate() || $form->fieldAccess->canEditJoinDate()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_join_date',
                                ['options' => ['class' => 'form-group']]
                            )->widget(
                                \dosamigos\datepicker\DatePicker::class,
                                [
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                    ],
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'placeholder' => 'Choose Date',
                                        'disabled' => !$form->fieldAccess->canEditJoinDate()
                                    ],
                                    ]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewSkill() || $form->fieldAccess->canEditSkill()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm
                                ->field($form, 'up_skill', ['options' => ['class' => 'form-group']])
                                ->dropDownList(\common\models\UserProfile::SKILL_TYPE_LIST, [
                                    'prompt' => '---',
                                    'disabled' => !$form->fieldAccess->canEditSkill()
                                ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewCallTypeId() || $form->fieldAccess->canEditCallTypeId()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_call_type_id',
                                ['options' => ['class' => 'form-group']]
                            )->dropDownList(
                                \common\models\UserProfile::CALL_TYPE_LIST,
                                ['disabled' => !$form->fieldAccess->canEditCallTypeId()]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canView2faSecret() || $form->fieldAccess->canEdit2faSecret()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_2fa_secret',
                                ['options' => ['class' => 'form-group']]
                            )->textInput([
                                'maxlength' => true,
                                'title' => 'Clean for reset',
                                'readonly' => !$form->fieldAccess->canEdit2faSecret()
                                ])->label('2fa secret') ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canView2faEnable() || $form->fieldAccess->canEdit2faEnable()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_2fa_enable',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEdit2faEnable()]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewTelegram() || $form->fieldAccess->canEditTelegram()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_telegram',
                                ['options' => ['class' => 'form-group']]
                            )->textInput([
                                'maxlength' => true,
                                'readonly' => !$form->fieldAccess->canEditTelegram()
                                ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewTelegramEnable() || $form->fieldAccess->canEditTelegramEnable()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_telegram_enable',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEditTelegramEnable()]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewAutoRedial() || $form->fieldAccess->canEditAutoRedial()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_auto_redial',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEditAutoRedial()]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewKpiEnable() || $form->fieldAccess->canEditKpiEnable()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_kpi_enable',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEditKpiEnable()]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewShowInContactList() || $form->fieldAccess->canEditShowInContactList()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_show_in_contact_list',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEditShowInContactList()]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canViewCallRecordingDisabled() || $form->fieldAccess->canEditCallRecordingDisabled()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_call_recording_disabled',
                                ['options' => ['class' => 'form-group']]
                            )->checkbox(['disabled' => !$form->fieldAccess->canEditCallRecordingDisabled()]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <?= Html::submitButton(
                '<i class="fa fa-save"></i> Update & Save User data',
                ['class' => 'btn btn-warning']
            ) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>

    <div class="col-sm-7">


        <div class="user-project-params-index">
            <h4>Project Params</h4>

            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-upp']); ?>
            <p>
                <?php echo Html::a(
                    '<i class="glyphicon glyphicon-plus"></i> Create Project Params',
                    null,
                    [
                        'class' => 'btn btn-success btn-xs act-create-upp',
                        'title' => 'Create Project Params',
                        //'data-toggle'=>'modal',
                        //'data-target'=>'#activity-modal',
                        'data-user_id' => $form->updaterUser->id,
                        'data-pjax' => '0',
                    ]
                );
?>
            </p>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $userProjectParamsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'upp_project_id',
                        'value' => function (\common\models\UserProjectParams $model) {
                            return $model->uppProject ? '' . $model->uppProject->name . '' : '-';
                        },
                        'filter' => $form->availableList->getProjects(),
                        //'format' => 'raw'
                        //'contentOptions' => ['class' => 'text-right']
                    ],

                    [
                        'attribute' => 'upp_dep_id',
                        'value' => function (\common\models\UserProjectParams $model) {
                            return $model->uppDep ? '' . $model->uppDep->dep_name . '' : '-';
                        },
                    ],

                    //'upp_user_id',
                    //'upp_project_id',
                    //'upp_email:email',
                    [
                        'class' => \common\components\grid\EmailSelect2Column::class,
                        'attribute' => 'upp_email_list_id',
                        'relation' => 'emailList',
                    ],
                    //'upp_phone_number',
                    //'upp_tw_phone_number',
                    [
                        'class' => \common\components\grid\PhoneSelect2Column::class,
                        'attribute' => 'upp_phone_list_id',
                        'relation' => 'phoneList',
                    ],
                    [
                        'attribute' => 'upp_allow_general_line',
                        'format' => 'raw',
                        'value' => function (\common\models\UserProjectParams $model) {
                            if ($model->upp_allow_general_line) {
                                return '<i class="fa fa-check-square-o"></i>';
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'upp_vm_enabled',
                        'format' => 'raw',
                        'value' => function (\common\models\UserProjectParams $model) {
                            if ($model->upp_vm_enabled) {
                                return '<i class="fa fa-check-square-o"></i>';
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'upp_vm_user_status_id',
                        'value' => static function (UserProjectParams $model) {
                            return UserProjectParams::VM_USER_STATUS_LIST[$model->upp_vm_user_status_id] ?? null;
                        },
                    ],
                    [
                        'attribute' => 'upp_vm_id',
                        'value' => static function (UserProjectParams $model) {
                            return $model->upp_vm_id ? $model->voiceMail->uvm_name : null;
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{update} {delete}',
                        'controller' => 'user-project-params',
                        //'headerOptions' => ['width' => '20%', 'class' => '',],
                        'buttons' => [
                            'update' => static function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
                                    'class' => 'act-update-upp text-warning',
                                    'title' => 'Update Project params',
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
                <?php echo Html::a(
                    '<i class="glyphicon glyphicon-plus"></i> Add Voice Mail',
                    null,
                    [
                        'class' => 'btn btn-success btn-xs add-voice-mail',
                        'title' => 'Add Voice Mail',
                        'data-user_id' => $form->updaterUser->id,
                        'data-pjax' => '0',
                    ]
                ) ?>
            </p>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $userVoiceMailProvider,
                'columns' => [
                    'uvm_name',
                    'uvm_say_language',
                    'uvm_record_enable:booleanByLabel',
                    'uvm_max_recording_time',
//                  'uvm_transcribe_enable:booleanByLabel',
//                  'uvm_enabled:booleanByLabel',
                    'uvm_created_dt:byUserDateTime',
                    'uvm_updated_dt:byUserDateTime',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {update} {delete}',
                        'controller' => 'user-voice-mail',
                        'buttons' => [
                            'update' => static function ($key) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
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

        <?php if (Auth::can('user-product-type/list')) : ?>
            <div class="user-product-type">
                <h4>Product Type</h4>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-product-type']); ?>

                <?php if (Auth::can('user-product-type/create')) : ?>
                    <p>
                        <?php echo Html::a(
                            '<i class="glyphicon glyphicon-plus"></i> Add Product Type',
                            null,
                            [
                                'class' => 'btn btn-success btn-xs add-product-type',
                                'title' => 'Add Product Type',
                                'data-user_id' => $form->updaterUser->id,
                                'data-user_name' => $form->updaterUser->username,
                                'data-pjax' => '0',
                            ]
                        ) ?>
                    </p>
                <?php endif ?>

                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataUserProductTypeDataProvider,
                    'columns' => [
                        [
                            'attribute' => 'upt_product_type_id',
                            'value' => function (UserProductType $model) {
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
                                        $updateButton = Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
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
                                        $deleteButton = Html::a(
                                            '<span class="glyphicon glyphicon-trash"></span>',
                                            $url,
                                            []
                                        );
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

            <?php if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) : ?>
                <div class="user-failed-login">
                    <h5>User Failed Login</h5>

                    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-user-failed']); ?>

                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataLastFailedLoginDataProvider,
                        'rowOptions' => static function (UserFailedLogin $UserFailedLogin, $index, $widget, $grid) {
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

        <?php endif; ?>
    </div>

<?php

$js = <<<JS

    //  $(document).on('click', '.unblock-user', function(e) {
    //      e.preventDefault();
    //    
    //      if(!confirm('Are you sure un-block this user?')) {
    //         return true;
    //      }
    //     
    //      let objBtn = $(this);
    //      let htmlInner = objBtn.html();
    //         
    //      $.ajax({
    //         type: 'post',
    //         url: '/user-failed-login/set-active-ajax',
    //         dataType: 'json',
    //         data: {id:objBtn.data('user_id')},                
    //         beforeSend: function () {                    
    //             objBtn.html('<span class="spinner-border spinner-border-sm"></span>');
    //             objBtn.prop('disabled', true);    
    //         },
    //         success: function (dataResponse) {            
    //             objBtn.prop('disabled', false);
    //             objBtn.html(htmlInner); 
    //              
    //             if (dataResponse.status === 1) {                        
    //                 objBtn.hide(); 
    //                 createNotifyByObject({
    //                     title: "Success",
    //                     type: "success",
    //                     text: dataResponse.message,
    //                     hide: true
    //                 });                      
    //             } else {                        
    //                 createNotifyByObject({
    //                     title: "Error:",
    //                     type: "error",
    //                     text: dataResponse.message,
    //                     hide: true
    //                 });
    //             }                       
    //         },
    //         error: function () {
    //             objBtn.prop('disabled', false);
    //             objBtn.html(htmlInner); 
    //         }
    //      });  
    // });

    $('#modal-df').on('hidden.bs.modal', function () {
        $.pjax.reload({container:'#pjax-grid-upp', 'async': false});
        $.pjax.reload({container:'#pjax-grid-voice-mail', 'async': false});
        $.pjax.reload({container: "#pjax-grid-product-type", 'async': false});
        
        /*createNotifyByObject({
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
