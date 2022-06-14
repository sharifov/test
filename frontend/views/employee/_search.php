<?php

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use src\auth\Auth;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\widgets\PhoneSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $model common\models\search\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>

                <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/ ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content"
             style="display: <?= (Yii::$app->request->isPjax || Yii::$app->request->get('EmployeeSearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none' ?>">
            <?php $form = ActiveForm::begin([
                'action' => ['list'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 profile_details">
                    <div class="well profile_view">
                        <div class="col-sm-12">
                            <h4 class="brief"><i>User common</i></h4>
                            <div class="row">
                                <div class="col-md-1">
                                    <?= $form->field($model, 'id') ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'username') ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'nickname') ?>
                                </div>

                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'email') ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'phoneListId', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(PhoneSelect2Widget::class, [
                                        'data' => $model->getPhoneListNumber()
                                    ])->label('Phone List'); ?>
                                </div>

                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'roles')
                                        ->widget(Select2::class, [
                                            'data' => Employee::getAllRoles(Auth::user()),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Role'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ]); ?>
                                </div>

                                <div class="col-md-1">
                                    <?php echo $form->field($model, 'status')->dropDownList(
                                        \common\models\Employee::STATUS_LIST,
                                        ['prompt' => '---']
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <div class=" profile-bottom text-center">
                        </div>
                    </div>
                </div>


                <div class="col-md-12 col-sm-12 profile_details">
                    <div class="well profile_view">
                        <div class="col-sm-12">
                            <h4 class="brief"><i>User profile</i></h4>
                            <div class="row">
                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'userGroupIds')
                                        ->widget(Select2::class, [
                                            'data' => \common\models\UserGroup::getList(),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select User Group'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ])->label('User Group'); ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'userDepartmentIds')
                                        ->widget(Select2::class, [
                                            'data' => \common\models\Department::getList(),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Department'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ])->label('User Department'); ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'projectAccessIds', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(Select2::class, [
                                        'data' => Project::getList(),
                                        'size' => Select2::SMALL,
                                        'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                                        'pluginOptions' => ['allowClear' => true],
                                    ])->label('User Project Access') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'projectParamsIds', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(Select2::class, [
                                        'data' => Project::getList(),
                                        'size' => Select2::SMALL,
                                        'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                                        'pluginOptions' => ['allowClear' => true],
                                    ])->label('User Project Params') ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'assignedShifts')
                                        ->widget(Select2::class, [
                                            'data' => \yii\helpers\ArrayHelper::map(UserShiftAssign::getAssignedShits(), 'sh_id', 'sh_name'),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Shift'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ]); ?>
                                </div>

                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'chatChannels')
                                        ->widget(Select2::class, [
                                            'data' => ClientChatChannel::getList(),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Chat Channel'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ]); ?>
                                </div>
                            </div>
                        </div>
                        <div class=" profile-bottom text-center">
                        </div>
                    </div>
                </div>


                <div class="col-md-12 col-sm-12 profile_details">
                    <div class="well profile_view">
                        <div class="col-sm-12">
                            <h4 class="brief"><i>User params</i></h4>
                            <div class="row">


                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'twoFaEnable')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '---']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'userTimezones')
                                        ->widget(Select2::class, [
                                            'data' => \common\models\UserParams::getActiveTimezones(),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Timezone'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ]); ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'skills')
                                        ->widget(Select2::class, [
                                            'data' => \common\models\UserProfile::SKILL_TYPE_LIST,
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => 'Select Skill'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ]); ?>
                                </div>
                                <div class="col-md-1">
                                    <?php echo $form->field($model, 'acl_rules_activated')->dropDownList(['' => '', 1 => 'Yes', 0 => 'No'])->label('IP filter') ?>
                                </div>
                                <div class="col-md-1">
                                    <?php echo $form->field($model, 'online')->dropDownList([1 => 'Online', 2 => 'Offline'], ['prompt' => '---']) ?>
                                </div>
                                <div class="col-md-1">
                                    <?php echo $form->field($model, 'callReady')->dropDownList(['' => '', 0 => 'Off', 1 => 'On'])->label('Call Ready') ?>
                                </div>

                                <div class="col-md-2">
                                    <?php echo $form->field($model, 'useTelegram')->dropDownList(['' => '', 1 => 'Yes', 0 => 'No']) ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'lastLoginRangeTime', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                                        'presetDropdown' => false,
                                        'hideInput' => true,
                                        'convertFormat' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-Y',
                                                'separator' => ' - '
                                            ]
                                        ]
                                    ])->label('Last Login Date From / To');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'createdRangeTime', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                                        'presetDropdown' => false,
                                        'hideInput' => true,
                                        'convertFormat' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-Y',
                                                'separator' => ' - '
                                            ]
                                        ]
                                    ])->label('Created Date From / To');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'updatedRangeTime', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                                        'presetDropdown' => false,
                                        'hideInput' => true,
                                       'convertFormat' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-Y',
                                                'separator' => ' - '
                                            ]
                                        ]
                                    ])->label('Updated Date From / To');
                                    ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'e_created_user_id', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(\src\widgets\UserSelect2Widget::class, [
                                    ]); ?>
                                </div>

                                <div class="col-md-2">
                                    <?= $form->field($model, 'e_updated_user_id', [
                                        'options' => ['class' => 'form-group']
                                    ])->widget(\src\widgets\UserSelect2Widget::class, [

                                    ]); ?>
                                </div>
                            </div>
                        </div>
                        <div class=" profile-bottom text-center">
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <h2><i class="fa fa-list"></i> Show Additional fields</h2>
                    <?php //echo Html::label('Additional fields:', 'showFields', ['class' => 'control-label']);?>
                    <?= //Select2::widget([
                    // 'name' => 'LeadSearch[show_fields]', //Html::getInputName($filter, 'showFilter'),
                    $form->field($model, 'show_fields')->widget(Select2::class, [
                        'data' => $model->getViewFields(),
                        'size' => Select2::SIZE_SMALL,
                        'pluginOptions' => [
                            'closeOnSelect' => false,
                            'allowClear' => true,
                            //'width' => '100%',
                        ],
                        'options' => [
                            'placeholder' => 'Choose additional fields...',
                            'multiple' => true,
                            'id' => 'showFields',
                        ],
                        //'value' => $model->show_fields,
                    ])->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['/employee/list'], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
