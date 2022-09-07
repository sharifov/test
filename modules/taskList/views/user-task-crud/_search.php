<?php

use common\models\Employee;
use common\models\UserGroup;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\userTask\UserTask;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?php echo $form->field($model, 'createTimeRange', [
                    'options' => ['class' => 'form-group']
                ])->widget(\kartik\daterange\DateRangePicker::class, [
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'createTimeStart',
                    'endAttribute' => 'createTimeEnd',
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ],
                    'id' => 'createTimeRange'
                ])->label('Created DateTime Range');
            ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'taskListIds', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => TaskList::getListCache(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select TaskList', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Task List') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'userTaskStatus', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => UserTask::STATUS_LIST,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select userTaskStatus', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Status User Task') ?>
        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'userTaskEmployee')->widget(Select2::class, [
                'data' => Employee::getActiveUsersList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select user', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('UserTask Employee'); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'userTaskUserGroup', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => UserGroup::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select User Group', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('UserTask User Group') ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
