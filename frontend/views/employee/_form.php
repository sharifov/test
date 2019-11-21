<?php

use yii\grid\ActionColumn;

/**
 * @var $this \yii\web\View
 * @var $modelUserParams \common\models\UserParams
 * @var $modelProfile \common\models\UserProfile
 */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\models\Employee */

use sales\access\EmployeeProjectAccess;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;
use common\models\EmployeeAcl;
use yii\widgets\MaskedInput;

$data = [];
$dataProjects = [];


if($model->isNewRecord) {
    $this->title = 'Create new User';
} else {
    $this->title ='Update user: ' . $model->username.' (ID:  '.$model->id.')';
}

$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;


if (Yii::$app->user->identity->canRoles(['admin', 'userManager', 'superadmin'])) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

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
                        <?php if($model->isNewRecord || Yii::$app->user->identity->canRoles(['admin', 'supervision', 'userManager', 'superadmin'])): ?>
                            <?//= $form->field($model, 'roles')->dropDownList($model::getAllRoles(), ['prompt' => '']) ?>

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
                        <?php if($model->isNewRecord || Yii::$app->user->identity->canRoles(['admin', 'supervision', 'agent', 'userManager', 'superadmin'])):


                            if(Yii::$app->user->identity->canRoles(['admin', 'userManager', 'superadmin'])) {
                                $data = \common\models\UserGroup::getList();
                                $dataProjects = \common\models\Project::getList();
                            }

                            if(Yii::$app->user->identity->canRole('supervision')) {
                                $data = Yii::$app->user->identity->getUserGroupList();
                                $dataProjects = \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->projects, 'id', 'name');
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

                <?php if(Yii::$app->user->identity->canRoles(['admin', 'supervision', 'superadmin'])): ?>

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
                            <?//= $form->field($modelUserParams, 'up_timezone')->dropDownList(Employee::timezoneList(),['prompt' =>'-'])?>
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
    $('#acl-rule-id').click(function() {
        $(this).addClass('d-none');
        $('#$idForm').removeClass('d-none');
    });

    $('#close-btn').click(function() {
        $('#acl-rule-id').removeClass('d-none');
        $('#$idForm').addClass('d-none');
    });

    $('#submit-btn').click(function() {
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
                            ])->widget(MaskedInput::class, [
                                'clientOptions' => [
                                    'alias' => 'ip'
                                ],
                            ]) ?>
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
                <?//= Html::a('Create User Project Params', ['user-project-params/create'], ['class' => 'btn btn-success']) ?>

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
                    //'upp_phone_number',
                    'upp_tw_phone_number',
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
    <?/*= $this->render('partial/_activities', [
        'model' => $model
    ])*/ ?>

    <?php /*
    if (!$model->isNewRecord && $model->role != 'admin') {
        echo $this->render('partial/_permissions', [
            'model' => $model,
            'isProfile' => $isProfile
        ]);
    }*/
    ?>
</div>

<?php \yii\bootstrap4\Modal::begin([
    'id' => 'activity-modal',
]); ?>
<?php \yii\bootstrap4\Modal::end(); ?>


<?php
$js = <<<JS

    $('#activity-modal').on('d-none.bs.modal', function () {
        // $('#modal-dialog').find('.modal-content').html('');
        $.pjax.reload({container:'#pjax-grid-upp'});
    });


    /*$("#update-app-pjax").on("pjax:end", function() {
        $.pjax.reload({container:'#pjax-grid-upp'});
        $('#activity-modal').modal('hide');
    });*/


    $(document).on('click', '.act-update-upp', function(e) {
        e.preventDefault();
        //alert(123);
        $.get(
            '/user-project-params/update-ajax',
            {
                data: $(this).closest('tr').data('key')
            },
            function (data) {
                $('#activity-modal .modal-content').html(data);
                $('#activity-modal').modal();
            }
        );
    });


    $(document).on('click', '.act-create-upp', function(e) {
        e.preventDefault();
        $.get('/user-project-params/create-ajax', {user_id: $(this).data('user_id')},
            function (data) {
                $('#activity-modal .modal-content').html(data);
                $('#activity-modal').modal();
            }
        );
    });

JS;
$this->registerJs($js);
