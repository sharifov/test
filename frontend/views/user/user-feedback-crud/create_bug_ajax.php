<?php

use kartik\date\DatePicker;
use kartik\time\TimePicker;
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
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'pageUrl')->textInput() ?>
            <?= $form->field($model, 'message')->textarea(['rows' => 10]) ?>
            <?= $form->field($model, 'data')->hiddenInput(['rows' => 10, 'id' => 'uf_data'])->label(false) ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'date')->widget(DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                        ]
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'time')->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'autoclose' => true,
                            'showMeridian' => false,
                            'minuteStep' => 1
                        ]
                    ]) ?>
                </div>
            </div>
            <div>
                <span data-toggle="collapse" href="#userBugReportData" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-info-circle"></i> Additional Data</span>
                <div id="userBugReportData" class="collapse"><pre></pre></div>
            </div>
        </div>
        <div class="col-md-5">
            <label class="control-label" for="userfeedbackbugform-screenshot">
                <?= $model->getAttributeLabel('screenshot') ?>
            </label>

            <img src="<?= Html::encode($model->screenshot) ?>" id="screenshot-img" style="width:100%" class="rounded mx-auto <?= $model->screenshot ? '' : 'hidden' ?>" alt="screenshot"/>
            <div style="text-align: right;">
                <button class="btn btn-sm btn-danger remove-screenshot" style="margin-top: 10px;"><i class="fa fa-trash"></i> Remove screenshot</button>
            </div>
            <div style="display: none;">
                <?= $form->field($model, 'screenshot')->hiddenInput(['id' => 'bug-screen'])->label(false) ?>
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

<?php
$js = <<<JS
$('body').off('click', '.remove-screenshot').on('click', '.remove-screenshot', function (e) {
    e.preventDefault();
    $('#screenshot-img').remove();
    $('#bug-screen').val('');
    $(this).remove();
});
JS;
$this->registerJs($js);
?>

<?php Pjax::end(); ?>
