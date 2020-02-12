<?php

use common\models\Department;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskCreatedType;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\QaTask */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="qa-task-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 't_gid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 't_object_type_id')->dropDownList(QaObjectType::getList(), ['prompt' => 'Select Object type']) ?>

        <?= $form->field($model, 't_object_id')->textInput() ?>

        <?= $form->field($model, 't_category_id')->dropDownList(QaTaskCategoryQuery::getList(), ['prompt' => 'Select category']) ?>

        <?= $form->field($model, 't_status_id')->dropDownList(QaTaskStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 't_rating')->dropDownList(QaTaskRating::getList(), ['prompt' => 'Select rating']) ?>

        <?= $form->field($model, 't_create_type_id')->dropDownList(QaTaskCreatedType::getList(), ['prompt' => 'Select created type']) ?>

        <?= $form->field($model, 't_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 't_department_id')->dropDownList(Department::DEPARTMENT_LIST, ['prompt' => 'Select department']) ?>

        <?= $form->field($model, 't_deadline_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 't_assigned_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select employee']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
