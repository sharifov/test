<?php

/**
 * @var \src\forms\emailReviewQueue\EmailReviewQueueForm $previewForm
 * @var $this yii\web\View
 * @var $displayActionBtns bool
 * @var $files array|null
 */

use dosamigos\ckeditor\CKEditor;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageEmailSendListWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>
<?php if ($displayActionBtns) : ?>
<div class="btn-wrapper text-left">
    <?= Html::button('<i class="fa fa-close"></i> Reject', ['class' => 'btn btn-lg btn-danger btn-submit', 'id' => 'reject-email-review-queue', 'data-url' => Url::to(['/email-review-queue/reject'])]) ?>
    <?= Html::button('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-lg btn-success btn-submit', 'id' => 'send-email-review-queue', 'data-url' => Url::to(['/email-review-queue/send'])]) ?>
</div>
<?php endif; ?>
<div class="x_panel">
    <div class="x_title" >
        <h2><i class="fa fa-sticky-note-o"></i> Review Email Form</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="margin-top: -10px;">
        <br>
        <div class="row">
            <div class="col-md-12">
                <?php Pjax::begin(['id' => 'pjax-email-review-queue', 'enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 10000]) ?>
                <?php $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                        'class' => 'panel-body',
                        'id' => 'form-email-review-queue'
                    ],
                ]);

                echo $form->errorSummary($previewForm);
?>

                <div class="row">
                    <div class="col-sm-6 form-group">

                        <?= $form->field($previewForm, 'emailFrom')->textInput(['class' => 'form-control', 'maxlength' => true, ]) ?>
                        <?= $form->field($previewForm, 'emailFromName')->textInput(['class' => 'form-control', 'maxlength' => true, ]) ?>


                        <?= $form->field($previewForm, 'emailIsNorm')->hiddenInput()->label(false); ?>
                        <?= $form->field($previewForm, 'emailId')->hiddenInput()->label(false); ?>
                        <?= $form->field($previewForm, 'emailQueueId')->hiddenInput()->label(false); ?>
                        <?= $form->field($previewForm, 'leadId')->hiddenInput()->label(false); ?>
                        <?= $form->field($previewForm, 'caseId')->hiddenInput()->label(false); ?>
                    </div>
                    <div class="col-sm-6 form-group">
                        <?= $form->field($previewForm, 'emailTo')->textInput(['class' => 'form-control', 'maxlength' => true, ]) ?>
                        <?= $form->field($previewForm, 'emailToName')->textInput(['class' => 'form-control', 'maxlength' => true, ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <?= $form->field($previewForm, 'emailSubject')->textInput(['class' => 'form-control', 'maxlength' => true]) ?>
                    </div>
                    <?php if ($files) : ?>
                    <div class="col-md-12 form-group">
                        <label class="control-label">Attached files</label>
                        <div class="form-group">
                            <?= implode(', ', array_map(static function ($file) {
                                return $file['name'];
                            }, $files)) ?>
                        </div>
                        <?php /* if (FileStorageSettings::canEmailAttach()) : ?>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <?= FileStorageEmailSendListWidget::byReview($previewForm->getFileList(), $previewForm->formName(), $previewForm->getSelectedFiles()) ?>
                                </div>
                            </div>
                        <?php endif; */ ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <?= $form->field($previewForm, 'emailMessage')->widget(CKEditor::class, [
                        'options' => [
                            'rows' => 6,
                            'readonly' => false,
                        ],
                        'preset' => 'full',
                        'clientOptions' => [
                            'height' => 500,
                            'fullPage' => true,

                            'allowedContent' => true,
                            'resize_enabled' => false,
                            'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                            'removePlugins' => 'elementspath',
                        ]
                    ]) ?>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php $messageSize = mb_strlen($previewForm->emailMessage) ?>
                        <b>Content size: <?=Yii::$app->formatter->asShortSize($messageSize, 1) ?></b>
                        <?php if ($messageSize > 102 * 1024) : ?>
                            &nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
                        <?php endif; ?>
                        <hr>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
                <?php Pjax::end()?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
let clickedBtn = '';
let clickedBtnHtml = '';
$(document).on('click', '.btn-submit', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-url');
    clickedBtn = $(this);
    clickedBtnHtml = clickedBtn.html();
    $(".btn-submit").attr("disabled", true).prop("disabled", true).addClass("disabled");
    $(this).find('i').attr("class", "fa fa-spinner fa-pulse fa-fw");
    $('#form-email-review-queue').attr('action', url).submit();
});
$('#pjax-email-review-queue').on('pjax:success', function () {
    $(".btn-submit").attr("disabled", false).prop("disabled", false).removeClass("disabled");
    clickedBtn.html(clickedBtnHtml);
});
JS;
$this->registerJs($js);
?>
