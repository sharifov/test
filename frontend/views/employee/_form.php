<?php
/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $modelUserParams \common\models\UserParams
 * @var $isProfile boolean
 */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;
use common\models\EmployeeAcl;
use yii\widgets\MaskedInput;

$formId = sprintf('%s-ID', $model->formName());

if($model->isNewRecord) {
    $this->title = 'Create new User';
} else {
    $this->title ='Update user: ' . $model->username.' (ID:  '.$model->id.')';
}

$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;


if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}


?>

<div class="col-sm-6">
    <?php $form = ActiveForm::begin([
        'successCssClass' => '',
        'id' => $formId
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
                <?php if (!$isProfile) : ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?php if($model->isNewRecord || Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ||
                            (Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id) && $model->role == 'agent')): ?>
                                <?= $form->field($model, 'role')->dropDownList($model::getAllRoles(), ['prompt' => '']) ?>
                            <?php else: ?>
                                <div>
                                <label class="control-label">Role</label>:
                                    <b><?=Html::encode($model->role);?></b>
                                </div>
                            <? endif; ?>
                        </div>
                        <?php if (!$model->isNewRecord) : ?>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'deleted', ['template' => '{label}{input}'])->checkbox() ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if($model->isNewRecord || Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ||
                            (Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id) && $model->role == 'agent')):

                            if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                                $data = \common\models\UserGroup::getList();
                                $dataProjects = \common\models\Project::getList();
                            }

                            if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
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

                        <? else: ?>

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

                        <? endif; ?>
                    </div>

                </div>

                <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $form->errorSummary($modelUserParams) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_base_amount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => '1000']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_commission_percent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_bonus_active')->checkbox() ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php
            if (!$model->isNewRecord && !$isProfile) : ?>
                <div class="well form-inline">
                    <div class="form-group">
                        <?= $form->field($model, 'acl_rules_activated', [
                            'template' => '{input}'
                        ])->checkbox() ?>
                        <span>&nbsp;</span>
                        <?= Html::a('Add Extra Rule', null, [
                            'class' => 'btn btn-success',
                            'id' => 'acl-rule-id',
                        ]) ?>
                        <?php
                        $aclModel = new EmployeeAcl();
                        $aclModel->employee_id = $model->id;
                        $idForm = sprintf('%s-ID', $aclModel->formName());
                        $idMaskIP = Html::getInputId($aclModel, 'mask');

                        $js = <<<JS
    $('#acl-rule-id').click(function() {
        $(this).addClass('hidden');
        $('#$idForm').removeClass('hidden');
    });

    $('#close-btn').click(function() {
        $('#acl-rule-id').removeClass('hidden');
        $('#$idForm').addClass('hidden');
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
                        <div class="form-group hidden" id="<?= $idForm ?>">
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
            <div class="form-group">
                <?= Html::submitButton(($model->isNewRecord ? 'Create User' : 'Update User'), ['class' => 'btn btn-primary']) ?>
            </div>
    <?php ActiveForm::end() ?>
</div>


<div class="col-sm-6">
    <?php if (!$model->isNewRecord) : ?>

        <div class="user-project-params-index">

            <h4>Project Params</h4>

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-upp']); ?>
            <p>
                <?//= Html::a('Create User Project Params', ['user-project-params/create'], ['class' => 'btn btn-success']) ?>

                <?php echo Html::a('<i class="glyphicon glyphicon-plus"></i> Create Project Params',
                    ['user-project-params/create-ajax','user_id' => $model->id, 'redirect' => 'employee/update?id='.$model->id],
                    [
                        'class' => 'btn btn-success act-create-upp',
                        'title' => 'Create Project Params',
                        //'data-toggle'=>'modal',
                        //'data-target'=>'#activity-modal',
                        'data-user_id' => $model->id,
                        'data-pjax' => '0',
                    ]
                );
                ?>



                <?php /*<div class="modal fade" id="modal-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content loader-lg">

                        </div>
                    </div>
                </div>*/ ?>


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

                    //'upp_user_id',
                    //'upp_project_id',
                    'upp_email:email',
                    'upp_phone_number',
                    'upp_tw_phone_number',
                    'upp_tw_sip_id',

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
                        'class' => 'yii\grid\ActionColumn',
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
        <div class="panel panel-default">
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

<?php \yii\bootstrap\Modal::begin([
    'id' => 'activity-modal',
    //'header' => '<h4 class="modal-title">View Image</h4>',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',

]); ?>
<?php \yii\bootstrap\Modal::end(); ?>


<?php
$js = <<<JS
    
    $('#activity-modal').on('hidden.bs.modal', function () {
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
        $.get(
            '/user-project-params/create-ajax',         
            {
                user_id: $(this).data('user_id')
            },
            function (data) {
                $('#activity-modal .modal-content').html(data);
                $('#activity-modal').modal();
            }  
        );
    });

JS;
$this->registerJs($js);
