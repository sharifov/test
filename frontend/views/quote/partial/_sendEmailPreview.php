<?php
/**
 * @var $email Email
 * @var $errors []
 */

use dosamigos\ckeditor\CKEditor;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


$alert = false;

$formId = sprintf('%s-formId', $email->formName());
$url = Url::to([
    'quote/send-quotes',
]);

$js = <<<JS
/***  Cancel card  ***/
    $('#cancel-sent-email').click(function (e) {
        e.preventDefault();
        var editBlock = $('#$formId');
        editBlock.parent().parent().removeClass('show');
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
    <?= $form->field($email,'quotes', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($email,'e_lead_id', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($email,'e_language_id', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($email,'e_project_id', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($email,'e_template_type_id', ['template' => '{input}'])->hiddenInput()?>
    <?= $form->field($email,'e_email_data', ['template' => '{input}'])->hiddenInput()?>
    <div class="row">
    	<div class="col-md-6"><?= $form->field($email,'e_email_from')->textInput([ 'readonly' => true])?></div>
    	<div class="col-md-6"><?= $form->field($email,'e_email_to')->textInput(['readonly' => true])?></div>
    </div>
    <div class="form-group">
    	<?= $form->field($email,'e_email_subject')?>
    </div>

    <div class="form-group">
        <?= CKEditor::widget([
                'name' => 'e_email_body_html',
                'value' => $email->getEmailBodyHtml(),
                'options' => [
                    'rows' => 6,
                    'readonly' => false
                ],
                'preset' => 'custom',
                'clientOptions' => [
                    'height' => 500,
                    'allowedContent' => true,
                    'resize_enabled' => false,
                    'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                    'removePlugins' => 'elementspath',
                ]
            ]
        );?>
    </div>
    <div class="btn-wrapper">
        <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
            'id' => 'cancel-sent-email',
            'class' => 'btn btn-danger'
        ]) ?>
        <?= Html::submitButton('<i class="fa fa-envelope"></i> Send', [
            'class' => 'btn btn-primary'
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
