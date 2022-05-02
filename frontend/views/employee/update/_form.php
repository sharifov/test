<?php

use common\models\EmployeeAcl;
use kartik\select2\Select2;
use modules\user\src\update\UpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $userProjectParamsDataProvider yii\data\ActiveDataProvider */
/* @var $userProductTypeDataProvider yii\data\ActiveDataProvider */
/* @var $form UpdateForm */
/* @var $userVoiceMailDataProvider \yii\data\ActiveDataProvider */
/* @var $lastFailedLoginDataProvider \yii\data\ActiveDataProvider */

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

    <?php if ($form->fieldAccess->canShowProfileWithParameters() || $form->targetUser->isBlocked() || $form->targetUser->isDeleted()) : ?>
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

            <?php if ($form->fieldAccess->canShow('username') || $form->fieldAccess->canShow('email')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('username')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm->field($form, 'username', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->textInput([
                                'autocomplete' => "new-user",
                                'readonly' => !$form->fieldAccess->canEdit('username')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('email')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm
                                ->field($form, 'email', ['options' => ['class' => 'form-group']])
                                ->input('email', ['readonly' => !$form->fieldAccess->canEdit('email')])
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('full_name') || $form->fieldAccess->canShow('password')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('full_name')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm
                                ->field($form, 'full_name', ['options' => ['class' => 'form-group']])
                                ->textInput(['readonly' => !$form->fieldAccess->canEdit('full_name')])
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('password')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm->field($form, 'password', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->passwordInput([
                                'autocomplete' => 'new-password',
                                'readonly' => !$form->fieldAccess->canEdit('password')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('nickname')) : ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $activeForm
                            ->field($form, 'nickname', ['options' => ['class' => 'form-group']])
                            ->textInput(['readonly' => !$form->fieldAccess->canEdit('nickname')])
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('form_roles') || $form->fieldAccess->canShow('status')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('form_roles')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm
                                ->field($form, 'form_roles', ['options' => ['class' => 'form-group']])
                                ->widget(Select2::class, [
                                    'data' => $form->availableList->getRoles(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select user roles', 'multiple' => true],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'disabled' => !$form->fieldAccess->canEdit('form_roles'),
                                    ],
                                ])
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('status')) : ?>
                        <div class="col-md-6">
                            <?= $activeForm
                                ->field($form, 'status', ['options' => ['class' => 'form-group']])
                                ->dropDownList(
                                    $form->availableList->getStatuses(),
                                    ['disabled' => !$form->fieldAccess->canEdit('status')]
                                )
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('user_groups')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('user_groups')
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('user_projects')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('user_projects')
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('user_departments')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('user_departments')
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('client_chat_user_channel')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('client_chat_user_channel')
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('user_shift_assigns')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('user_shift_assigns')
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('up_work_start_tm') || $form->fieldAccess->canShow('up_work_minutes') || $form->fieldAccess->canShow('up_timezone')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('up_work_start_tm')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('up_work_start_tm')
                                    ]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_work_minutes')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_work_minutes', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input(
                                'number',
                                ['step' => 10, 'min' => 0, 'readonly' => !$form->fieldAccess->canEdit('up_work_minutes')]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_timezone')) : ?>
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
                                    'disabled' => !$form->fieldAccess->canEdit('up_timezone')
                                ],
                                ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('up_base_amount') || $form->fieldAccess->canShow('up_commission_percent') || $form->fieldAccess->canShow('up_bonus_active') || $form->fieldAccess->canShow('up_leaderboard_enabled')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('up_base_amount')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_base_amount', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 0.01,
                                'min' => 0,
                                'max' => 1000,
                                'readonly' => !$form->fieldAccess->canEdit('up_base_amount')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_commission_percent')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_commission_percent', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'max' => 100,
                                'min' => 0,
                                'readonly' => !$form->fieldAccess->canEdit('up_commission_percent')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_bonus_active')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_bonus_active', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_bonus_active')]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_leaderboard_enabled')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_leaderboard_enabled', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_leaderboard_enabled')]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('up_inbox_show_limit_leads') || $form->fieldAccess->canShow('up_default_take_limit_leads') || $form->fieldAccess->canShow('up_min_percent_for_take_leads') || $form->fieldAccess->canShow('up_frequency_minutes')) : ?>
                <hr>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('up_inbox_show_limit_leads')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_inbox_show_limit_leads', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'min' => 0,
                                'max' => 500,
                                'readonly' => !$form->fieldAccess->canEdit('up_inbox_show_limit_leads')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_default_take_limit_leads')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_default_take_limit_leads', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'max' => 100,
                                'min' => 0,
                                'readonly' => !$form->fieldAccess->canEdit('up_default_take_limit_leads')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_min_percent_for_take_leads')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_min_percent_for_take_leads', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'max' => 100,
                                'min' => 0,
                                'readonly' => !$form->fieldAccess->canEdit('up_min_percent_for_take_leads')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_frequency_minutes')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_frequency_minutes', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'max' => 1000,
                                'min' => 0,
                                'readonly' => !$form->fieldAccess->canEdit('up_frequency_minutes')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($form->fieldAccess->canShow('up_call_expert_limit') || $form->fieldAccess->canShow('up_call_user_level')) : ?>
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('up_call_expert_limit')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_call_expert_limit', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'min' => -1,
                                'max' => 1000,
                                'readonly' => !$form->fieldAccess->canEdit('up_call_expert_limit')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($form->fieldAccess->canShow('up_call_user_level')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field($form, 'up_call_user_level', [
                                'options' => [
                                    'class' => 'form-group'
                                ]
                            ])->input('number', [
                                'step' => 1,
                                'min' => -128,
                                'max' => 127,
                                'readonly' => !$form->fieldAccess->canEdit('up_call_user_level')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($form->fieldAccess->canShow('acl_rules_activated')) : ?>
        <div class="well">
            <div class="form-group">
                <?= $activeForm->field($form, 'acl_rules_activated', [
                    'template' => '{input}'
                ])->checkbox(['disabled' => !$form->fieldAccess->canEdit('acl_rules_activated')]) ?>
                <?php if ($form->fieldAccess->canEdit('acl_rules_activated')) : ?>
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
                    'canEditAclRulesActivated' => $form->fieldAccess->canEdit('acl_rules_activated'),
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($form->fieldAccess->canShowProfileSettings()) : ?>
        <h5>Profile Settings</h5>
        <div class="well">
            <div class="form-group">
                <div class="row">
                    <?php if ($form->fieldAccess->canShow('up_join_date') || $form->fieldAccess->canShow('up_skill')) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShow('up_join_date')) : ?>
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
                                            'disabled' => !$form->fieldAccess->canEdit('up_join_date')
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_skill')) : ?>
                                    <?= $activeForm
                                        ->field($form, 'up_skill', ['options' => ['class' => 'form-group']])
                                        ->dropDownList($form->availableList->getSkillTypes(), [
                                            'prompt' => '---',
                                            'disabled' => !$form->fieldAccess->canEdit('up_skill')
                                        ]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShow('up_2fa_secret') || $form->fieldAccess->canShow('up_2fa_enable')) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShow('up_2fa_secret')) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_2fa_secret',
                                    ['options' => ['class' => 'form-group']]
                                )->textInput([
                                    'maxlength' => true,
                                    'title' => 'Clean for reset',
                                    'readonly' => !$form->fieldAccess->canEdit('up_2fa_secret')
                                ])->label('2fa secret') ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_2fa_enable')) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_2fa_enable',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_2fa_enable')]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShow('up_call_type_id')) : ?>
                        <div class="col-md-3">
                            <?= $activeForm->field(
                                $form,
                                'up_call_type_id',
                                ['options' => ['class' => 'form-group']]
                            )->dropDownList(
                                $form->availableList->getCallTypes(),
                                ['disabled' => !$form->fieldAccess->canEdit('up_call_type_id')]
                            ) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShow('up_telegram') || $form->fieldAccess->canShow('up_telegram_enable')) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShow('up_telegram')) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_telegram',
                                    ['options' => ['class' => 'form-group']]
                                )->textInput([
                                    'maxlength' => true,
                                    'readonly' => !$form->fieldAccess->canEdit('up_telegram')
                                    ]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_telegram_enable')) : ?>
                                <?= $activeForm->field(
                                    $form,
                                    'up_telegram_enable',
                                    ['options' => ['class' => 'form-group']]
                                )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_telegram_enable')]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($form->fieldAccess->canShow('up_auto_redial') || $form->fieldAccess->canShow('up_kpi_enable') || $form->fieldAccess->canShow('up_show_in_contact_list') || $form->fieldAccess->canShow('up_call_recording_disabled')) : ?>
                        <div class="col-md-3">
                            <?php if ($form->fieldAccess->canShow('up_auto_redial')) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_auto_redial',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_auto_redial')]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_kpi_enable')) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_kpi_enable',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_kpi_enable')]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_show_in_contact_list')) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_show_in_contact_list',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_show_in_contact_list')]) ?>
                            <?php endif; ?>
                            <?php if ($form->fieldAccess->canShow('up_call_recording_disabled')) : ?>
                                    <?= $activeForm->field(
                                        $form,
                                        'up_call_recording_disabled',
                                        ['options' => ['class' => 'form-group']]
                                    )->checkbox(['disabled' => !$form->fieldAccess->canEdit('up_call_recording_disabled')]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Update & Save User data', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?php
echo $this->render('_additional', [
        'form' => $form,
        'userProjectParamsDataProvider' => $userProjectParamsDataProvider,
        'userVoiceMailDataProvider' => $userVoiceMailDataProvider,
        'lastFailedLoginDataProvider' => $lastFailedLoginDataProvider,
        'userProductTypeDataProvider' => $userProductTypeDataProvider,
]);
