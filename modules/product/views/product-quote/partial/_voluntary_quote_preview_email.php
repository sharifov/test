<?php

/** @var $previewEmailForm VoluntaryChangeQuotePreviewEmailForm */
/** @var $case \src\entities\cases\Cases */
/** @var $this \yii\web\View */

use modules\product\src\forms\ReprotectionQuotePreviewEmailForm;
use modules\product\src\forms\VoluntaryChangeQuotePreviewEmailForm;
use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/product/product-quote/voluntary-quote-send-email']);
?>

  <script>
      pjaxOffFormSubmit('#reprotection_quote_choose_cliet_pjax');
      pjaxOffFormSubmit('#reprotection_quote_preview_email_pjax');
  </script>
<?php
\yii\widgets\Pjax::begin([
    'id' => 'reprotection_quote_preview_email_pjax',
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout' => 3000,
]);
?>
<?php $activeForm = \yii\bootstrap\ActiveForm::begin([
    'options' => [
        'data-pjax' => 1,
        'class' => 'panel-body',
    ],
    'action' => [$url]
]);

echo $activeForm->errorSummary($previewEmailForm);
?>

    <div class="row">
        <div class="col-sm-6 form-group">

            <?= $activeForm->field($previewEmailForm, 'email_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
            <?= $activeForm->field($previewEmailForm, 'email_from_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>

            <?= $activeForm->field($previewEmailForm, 'case_id')->hiddenInput()->label(false); ?>

            <?= $activeForm->field($previewEmailForm, 'language_id')->hiddenInput()->label(false); ?>
            <?= $activeForm->field($previewEmailForm, 'email_tpl_id')->hiddenInput()->label(false); ?>

            <?= $activeForm->field($previewEmailForm, 'changeId')->hiddenInput()->label(false); ?>
            <?= $activeForm->field($previewEmailForm, 'originQuoteId')->hiddenInput()->label(false); ?>

        </div>
        <div class="col-sm-6 form-group">
            <?= $activeForm->field($previewEmailForm, 'email_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
            <?= $activeForm->field($previewEmailForm, 'email_to_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 form-group">
            <?= $activeForm->field($previewEmailForm, 'email_subject')->textInput(['class' => 'form-control', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= $activeForm->field($previewEmailForm, 'email_message')->widget(\dosamigos\ckeditor\CKEditor::class, [
            'options' => [
                'rows' => 6,
                'readonly' => false
            ],
            'preset' => 'custom',
            'clientOptions' => [
                'height' => 500,
                'fullPage' => true,

                'allowedContent' => true,
                'resizenabled' => false,
                'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                'removePlugins' => 'elementspath',
            ]
        ]) ?>
    </div>
    <div class="row" style="display: none" id="email-data-content-div">
    <pre>
        <?php // json_encode($previewEmailForm->content_data)?>
    </pre>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php $messageSize = mb_strlen($previewEmailForm->email_message) ?>
            <b>Content size: <?=Yii::$app->formatter->asShortSize($messageSize, 1) ?></b>
            <?php if ($messageSize > 102 * 1024) : ?>
                &nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
            <?php endif; ?>
            <hr>
        </div>
    </div>

    <div class="btn-wrapper text-right">
        <?= Html::button('<i class="fa fa-close"></i> Cancel', ['class' => 'btn btn-sm btn-danger', 'data-dismiss' => 'modal']) ?>
        <?= Html::button('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-sm btn-success', 'id' => 'reprotection-quote-send-email-btn']) ?>
        <?php // Html::button('<i class="fa fa-list"></i> Show Email data (for Admins)', ['class' => 'btn btn-lg btn-warning', 'onclick' => '$("#email-data-content-div").toggle()'])?>
    </div>
<?php \yii\bootstrap\ActiveForm::end(); ?>

<?php \yii\widgets\Pjax::end() ?>

<?php

$js = <<<JS
$('body').off('click', '#reprotection-quote-send-email-btn').on('click', '#reprotection-quote-send-email-btn', function (e) {
    e.preventDefault();
    $('#reprotection-quote-send-email-btn').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#reprotection-quote-send-email-btn').addClass('disabled').prop('disabled', true);
    $(this).closest('form').submit();
});
JS;
$this->registerJs($js);
