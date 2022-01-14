<?php

use common\models\Department;
use common\models\Project;
use src\model\emailReviewQueue\entity\EmailReviewQueueStatus;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\emailReviewQueue\entity\EmailReviewQueue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-review-queue-form">

    <div class="row">
      <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'erq_email_id')->textInput() ?>

        <?= $form->field($model, 'erq_project_id')->dropDownList(Project::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'erq_department_id')->dropDownList(Department::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'erq_owner_id')->widget(UserSelect2Widget::class, [
            'data' => $model->erq_owner_id ? [
                $model->erq_owner_id => $model->erqOwner->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'erq_status_id')->dropDownList(EmailReviewQueueStatus::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'erq_user_reviewer_id')->widget(UserSelect2Widget::class, [
            'data' => $model->erq_user_reviewer_id ? [
                $model->erq_user_reviewer_id => $model->erqUserReviewer->username
            ] : [],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>

</div>
