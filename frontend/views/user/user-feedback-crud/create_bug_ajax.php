<?php

use modules\user\userFeedback\forms\UserFeedbackBugForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model UserFeedbackBugForm */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
    pjaxOffFormSubmit('#user-feedback-pjax');
</script>

<?php Pjax::begin(['id' => 'user-feedback-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => ['user-feedback-crud/create-ajax'],
    'method' => 'post',
]) ?>

<div class="col-md-12">
    <h3><i class="fa fa-bug"></i> Bug Report</h3>
</div>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-7">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'message')->textarea(['rows' => 10]) ?>
            <?= $form->field($model, 'data')->textarea(['rows' => 10, 'id' => 'uf_data']) ?>
        </div>
        <div class="col-md-5">
            <label class="control-label" for="userfeedbackbugform-screenshot">
                <?= $model->getAttributeLabel('screenshot') ?>
            </label>

            <img src="<?= Html::encode($model->screenshot) ?>" id="screenshot-img" style="width:100%" class="rounded mx-auto d-block" alt="screenshot"/>
            <div style="display: none;">
                <?= $form->field($model, 'screenshot')->hiddenInput()->label(false)?>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('Send feedback', ['class' => 'btn btn-success'])?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
