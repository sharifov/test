<?php

use common\models\EmployeeAcl;
use kartik\select2\Select2;
use modules\user\src\update\UpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $userProjectParamsDataProvider yii\data\ActiveDataProvider */
/* @var $dataUserProductTypeDataProvider yii\data\ActiveDataProvider */
/* @var $form UpdateForm */
/* @var $userVoiceMailDataProvider \yii\data\ActiveDataProvider */
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
        'validationUrl' => Url::to(['employee/employee-validation-update', 'id' => (int)$form->targetUser->id]),
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

        <?php if ($form->fieldAccess->canShowUsername() || $form->fieldAccess->canShowEmail()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowUsername()) : ?>
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
                <?php if ($form->fieldAccess->canShowEmail()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'email', ['options' => ['class' => 'form-group']])
                            ->input('email', ['readonly' => !$form->fieldAccess->canEditEmail()])
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowFullName() || $form->fieldAccess->canShowPassword()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowFullName()) : ?>
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'full_name', ['options' => ['class' => 'form-group']])
                            ->textInput(['readonly' => !$form->fieldAccess->canEditFullName()])
                        ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canShowPassword()) : ?>
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
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowNickname()) : ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $activeForm
                        ->field($form, 'nickname', ['options' => ['class' => 'form-group']])
                        ->textInput(['readonly' => !$form->fieldAccess->canEditNickname()])
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowRoles() || $form->fieldAccess->canShowStatus()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowRoles()) : ?>
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
                <?php if ($form->fieldAccess->canShowStatus()) : ?>
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
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowUserGroups()) : ?>
            <div class="row">
                <div class="col-md-12">
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
                        ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowProjects()) : ?>
            <div class="row">
                <div class="col-md-12">
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
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowDepartments()) : ?>
            <div class="row">
                <div class="col-md-12">
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
                        ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowClientChatUserChannels()) : ?>
            <div class="row">
                <div class="col-md-12">
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
                        ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowUserShiftAssign()) : ?>
            <div class="row">
                <div class="col-md-12">
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
                                'disabled' => !$form->fieldAccess->canEditUserShiftAssign()
                            ],
                        ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowWorkStartTime() || $form->fieldAccess->canShowWorkMinutes() || $form->fieldAccess->canShowTimeZone()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowWorkStartTime()) : ?>
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
                <?php if ($form->fieldAccess->canShowWorkMinutes()) : ?>
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
                <?php if ($form->fieldAccess->canShowTimeZone()) : ?>
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
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowBaseAmount() || $form->fieldAccess->canShowCommissionPercent() || $form->fieldAccess->canShowBonusActive() || $form->fieldAccess->canShowLeaderboardEnabled()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowBaseAmount()) : ?>
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
                <?php if ($form->fieldAccess->canShowCommissionPercent()) : ?>
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
                <?php if ($form->fieldAccess->canShowBonusActive()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_bonus_active', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->checkbox(['disabled' => !$form->fieldAccess->canEditBonusActive()]) ?>
                    </div>
                <?php endif; ?>
                <?php if ($form->fieldAccess->canShowLeaderboardEnabled()) : ?>
                    <div class="col-md-3">
                        <?= $activeForm->field($form, 'up_leaderboard_enabled', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->checkbox(['disabled' => !$form->fieldAccess->canEditLeaderboardEnabled()]) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowInboxShowLimitLeads() || $form->fieldAccess->canShowDefaultTakeLimitLeads() || $form->fieldAccess->canShowMinPercentForTakeLeads() || $form->fieldAccess->canShowFrequencyMinutes()) : ?>
            <hr>
            <div class="row">
                <?php if ($form->fieldAccess->canShowInboxShowLimitLeads()) : ?>
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
                <?php if ($form->fieldAccess->canShowDefaultTakeLimitLeads()) : ?>
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
                <?php if ($form->fieldAccess->canShowMinPercentForTakeLeads()) : ?>
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
                <?php if ($form->fieldAccess->canShowFrequencyMinutes()) : ?>
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
        <?php endif; ?>

        <?php if ($form->fieldAccess->canShowCallExpertLimit() || $form->fieldAccess->canShowCallUserLevel()) : ?>
            <div class="row">
                <?php if ($form->fieldAccess->canShowCallExpertLimit()) : ?>
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
                <?php if ($form->fieldAccess->canShowCallUserLevel()) : ?>
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
        <?php endif; ?>
    </div>

    <?php if ($form->fieldAccess->canShowAclRulesActivated()) : ?>
        <div class="well">
            <div class="form-group">
                <?= $activeForm->field($form, 'acl_rules_activated', [
                    'template' => '{input}'
                ])->checkbox(['disabled' => !$form->fieldAccess->canEditAclRulesActivated()]) ?>
                <?php if ($form->fieldAccess->canEditAclRulesActivated()) : ?>
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
                            ])->textInput(['maxlength' => true]) ?>
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
                <?php endif;?>
            </div>
            <div class="grid-view" id="employee-acl-rule" style="padding-top: 10px;">
                <?= $this->render('_aclList', [
                    'models' => $form->targetUser->employeeAcl,
                    'canEditAclRulesActivated' => $form->fieldAccess->canEditAclRulesActivated(),
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($form->fieldAccess->canShowProfileSettings()) : ?>
        <h5>Profile Settings</h5>
        <div class="well">
            <div class="form-group">
                <div class="row">
                    <?php if ($form->fieldAccess->canShowJoinDate() || $form->fieldAccess->canShowSkill()) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShowJoinDate()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_join_date',
                                        ['options' => ['class' => 'form-group']]
                                    )->widget(\dosamigos\datepicker\DatePicker::class, [
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd',
                                        ],
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'placeholder' => 'Choose Date',
                                            'disabled' => !$form->fieldAccess->canEditJoinDate()
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShowSkill()) : ?>
                                    <?= $activeForm
                                        ->field($form, 'up_skill', ['options' => ['class' => 'form-group']])
                                        ->dropDownList(\common\models\UserProfile::SKILL_TYPE_LIST, [
                                            'prompt' => '---',
                                            'disabled' => !$form->fieldAccess->canEditSkill()
                                        ]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShow2faEnable() || $form->fieldAccess->canShow2faSecret()) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShow2faSecret()) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_2fa_secret',
                                    ['options' => ['class' => 'form-group']]
                                )->textInput([
                                    'maxlength' => true,
                                    'title' => 'Clean for reset',
                                    'readonly' => !$form->fieldAccess->canEdit2faSecret()
                                ])->label('2fa secret') ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow2faEnable()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_2fa_enable',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit2faEnable()]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShowCallTypeId()) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_call_type_id',
                                ['options' => ['class' => 'form-group']]
                            )->dropDownList(
                                $form->availableList->getCallTypes(),
                                ['disabled' => !$form->fieldAccess->canEditCallTypeId()]
                            ) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShowTelegram() || $form->fieldAccess->canShowTelegramEnable()) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShowTelegram()) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_telegram',
                                    ['options' => ['class' => 'form-group']]
                                )->textInput([
                                    'maxlength' => true,
                                    'readonly' => !$form->fieldAccess->canEditTelegram()
                                    ]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShowTelegramEnable()) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_telegram_enable',
                                    ['options' => ['class' => 'form-group']]
                                )->checkbox(['disabled' => !$form->fieldAccess->canEditTelegramEnable()]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShowAutoRedial() || $form->fieldAccess->canShowKpiEnable() || $form->fieldAccess->canShowInContactList() || $form->fieldAccess->canShowCallRecordingDisabled()) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShowAutoRedial()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_auto_redial',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEditAutoRedial()]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShowKpiEnable()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_kpi_enable',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEditKpiEnable()]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShowInContactList()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_show_in_contact_list',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEditShowInContactList()]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShowCallRecordingDisabled()) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_call_recording_disabled',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEditCallRecordingDisabled()]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group text-center">
        <?= Html::submitButton(
            '<i class="fa fa-save"></i> Update & Save User data',
            ['class' => 'btn btn-warning']
        ) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?php
echo $this->render('_additional', [
        'form' => $form,
        'userProjectParamsDataProvider' => $userProjectParamsDataProvider,
        'userVoiceMailDataProvider' => $userVoiceMailDataProvider,
        'dataLastFailedLoginDataProvider' => $dataLastFailedLoginDataProvider,
        'dataUserProductTypeDataProvider' => $dataUserProductTypeDataProvider,
]);
