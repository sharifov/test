<?php
/**
 * @var $previewEmailModel PreviewEmailQuotesForm
 * @var $errors []
 */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


$alert = false;

$formId = sprintf('%s-formId', $previewEmailModel->formName());
$url = Url::to([
    'quote/send-quotes',
]);

$js = <<<JS
/***  Cancel card  ***/
    $('#cancel-sent-email').click(function (e) {
        e.preventDefault();
        var editBlock = $('#$formId');
        editBlock.parent().parent().removeClass('in');
        editBlock.parent().html('');
        $('#preview-send-quotes').modal('hide');
    });

    $('#$formId').on('beforeSubmit', function () {
        $('#preloader').removeClass('hidden');
        setTimeout(function() {
            $('#cancel-sent-email').trigger('click');
        });
    });
JS;
$this->registerJs($js);

if (empty($errors)) :
    $form = ActiveForm::begin([
        'id' => $formId
    ]) ?>
    <div class="form-group">
    	<?= $form->field($previewEmailModel,'subject')?>
    </div>
    <div class="form-group">
		<?= $form->field($previewEmailModel, 'body')->widget(\dosamigos\ckeditor\CKEditor::class, [
            'options' => [
                'rows' => 6,
                'readonly' => false
            ],
            'preset' => 'custom',
            'clientOptions' => [
                'height' => 500,
                /*'toolbarGroups' => [
                    ['name' => 'basicstyles'],
                ],*/
                'allowedContent' => true,
                'resize_enabled' => false,
                'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                'removePlugins' => 'elementspath',
            ]
        ]) ?>
    </div>
    <?= $form->field($previewEmailModel,'leadId', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($previewEmailModel,'email', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($previewEmailModel,'quotes', ['template' => '{input}'])->hiddenInput()?>
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
            'id' => 'cancel-sent-email',
            'class' => 'btn btn-danger btn-with-icon'
        ]) ?>
        <?= Html::submitButton('<span class="btn-icon"><i class="fa fa-envelope"></i></span><span>Send</span>', [
            'class' => 'btn btn-primary btn-with-icon'
        ]) ?>
    </div>
    <?php \yii\widgets\ActiveForm::end();
else :
    ?>
    <div class="row text-center">
    	<?php foreach ($errors as $error):?>
    	<div class="alert alert-warning"><?= $error?></div>
    	<?php endforeach;?>
    </div>
<?php endif; ?>
