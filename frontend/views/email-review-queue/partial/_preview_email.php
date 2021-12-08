<?php

/**
 * @var \sales\forms\emailReviewQueue\EmailReviewQueueForm $previewForm
 * @var $this yii\web\View
 */

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>
<div class="btn-wrapper text-left">
    <?= Html::button('<i class="fa fa-close"></i> Reject', ['class' => 'btn btn-lg btn-danger btn-submit', 'id' => 'reject-email-review-queue', 'data-url' => Url::to(['/email-review-queue/reject'])]) ?>
    <?= Html::button('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-lg btn-success btn-submit', 'id' => 'send-email-review-queue', 'data-url' => Url::to(['/email-review-queue/send'])]) ?>
</div>
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


                        <?= $form->field($previewForm, 'emailId')->hiddenInput()->label(false); ?>
                        <?= $form->field($previewForm, 'emailQueueId')->hiddenInput()->label(false); ?>
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
$(document).on('click', '.btn-submit', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-url');
    $(".btn-submit").attr("disabled", true).prop("disabled", true).addClass("disabled");
    $(this).find('i').attr("class", "fa fa-spinner fa-pulse fa-fw");
    $('#form-email-review-queue').attr('action', url).submit();
})
JS;
$this->registerJs($js);
?>
