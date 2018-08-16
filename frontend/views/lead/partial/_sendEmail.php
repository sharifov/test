<?php
/**
 * @var $templates []
 * @var $sendEmailModel SendEmailForm
 * @var $lead Lead
 * @var $preview bool
 * @var $sellerContactInfo EmployeeContactInfo
 */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use frontend\models\SendEmailForm;
use common\models\ProjectEmailTemplate;
use common\models\Lead;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\EmployeeContactInfo;


$alert = false;
$sellerContactInfo = EmployeeContactInfo::findOne([
    'employee_id' => $lead->employee_id,
    'project_id' => $lead->project_id
]);
if ($sellerContactInfo === null ||
    empty($sellerContactInfo->direct_line) ||
    empty($sellerContactInfo->email_pass)
) {
    $alert = true;
}

$emails = ArrayHelper::map($lead->client->clientEmails, 'email', 'email');
$formId = sprintf('%s-formId', $sendEmailModel->formName());
$url = Url::to([
    'lead/send-email',
    'id' => $lead->id
]);

$js = <<<JS
/***  Cancel card  ***/
    $('#cancel-sent-email').click(function (e) {
        e.preventDefault();
        var editBlock = $('#$formId');
        editBlock.parent().parent().removeClass('in');
        editBlock.parent().html('');
        $('#create-quote').modal('hide');
    });
    
    $('#sendemailform-type').change(function(e) {
        var url = '$url&type='+$(this).val();
        var editBlock = $('#create-quote');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) { });
    });
    
    $('#preview-email').click(function () {
        $('#$formId').yiiActiveForm('validateAttribute', 'sendemailform-subject');
        if ($('#sendemailform-type').val() == '_email_sales_free_form') {
            if ($.trim($('#cke_sendemailform-extrabody iframe').contents().find("body").text()).length == 0) {
                $('.field-sendemailform-extrabody').addClass('has-error');
            } else {
                $('.field-sendemailform-extrabody').removeClass('has-error');
            }
        }
        if ($('.has-error').length == 0) {
            var url = '$url&type='+$('#sendemailform-type').val();
            var editBlock = $('#create-quote');
            editBlock.find('.modal-body').load(url, {
                subject: $('#sendemailform-subject').val(), 
                extra_body: $('#sendemailform-extrabody').val()
            }, function( response, status, xhr ) { });
            return true;
        } else {
            return false;
        }
    });
    
    $('#$formId').on('beforeSubmit', function () {
        $('#preloader').removeClass('hidden');
        setTimeout(function() {
            console.log(1);
            $('#cancel-sent-email').trigger('click');
        });
    });
JS;
$this->registerJs($js);

if (!$alert) :
    $form = ActiveForm::begin([
        'id' => $formId
    ]) ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($sendEmailModel, 'type', [
                'template' => '{label}<div class="select-wrap-label">{input}</div>'
            ])->dropDownList($templates, [
                'prompt' => 'Select template',
            ]) ?>
        </div>
        <?php if (!empty($sendEmailModel->type)) :
            if (count($emails) == 1) {
                $sendEmailModel->emailTo = array_values($emails)[0];
            }
            ?>
            <div class="col-sm-8">
                <?= $form->field($sendEmailModel, 'emailTo', [
                    'template' => '{label}{input}'
                ])->dropDownList($emails, [
                    'prompt' => 'Select email',
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($sendEmailModel->type == ProjectEmailTemplate::TYPE_SALES_FREE_FORM) : ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($sendEmailModel, 'subject', [
                'template' => '{label}{input}'
            ])->textInput([
                'readonly' => $preview
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php if (!$preview) : ?>
                <?php $bodyPart = explode('{body}', $sendEmailModel->body) ?>
                <?= $form->field($sendEmailModel, 'extraBody', [
                    'template' => '{label}<div class="mb-20" style="border: 1px solid; padding: 5px;">' . $bodyPart[0] . '{input}' . $bodyPart[1] . '</div>'
                ])->widget(\dosamigos\ckeditor\CKEditor::class, [
                    'options' => [
                        'rows' => 6,
                        'readonly' => false
                    ],
                    'preset' => 'custom',
                    'clientOptions' => [
                        'height' => 300,
                        'toolbarGroups' => [
                            ['name' => 'basicstyles'],
                        ],
                        'allowedContent' => true,
                        'resize_enabled' => false,
                        'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                        'removePlugins' => 'elementspath',
                    ]
                ]) ?>
            <?php else : ?>
                <label><?= Html::label($sendEmailModel->getAttributeLabel('body')) ?></label>
                <div class="mb-20" style="border: 1px solid; padding: 5px;">
                    <?= $form->field($sendEmailModel, 'extraBody', [
                        'template' => '{input}'
                    ])->hiddenInput() ?>
                    <?= $sendEmailModel->body ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="btn-wrapper">
        <?php
        echo Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
                'id' => 'cancel-sent-email',
                'class' => 'btn btn-danger btn-with-icon'
            ]) . ' ';
        if ($preview) {
            echo Html::submitButton('<span class="btn-icon"><i class="fa fa-envelope"></i></span><span>Send</span>', [
                'class' => 'btn btn-primary btn-with-icon'
            ]);
        } else {
            echo Html::button('<span class="btn-icon"><i class="fa fa-eye"></i></span><span>Preview</span>', [
                'id' => 'preview-email',
                'class' => 'btn btn-primary btn-with-icon'
            ]);
        }
        ?>
    </div>
<?php else: ?>
    <?php if (!empty($sendEmailModel->type)) : ?>
        <div class="row">
            <div class="col-sm-12">
                <label><?= Html::label($sendEmailModel->getAttributeLabel('subject')) ?></label>
                <div class="mb-20" style="border: 1px solid; padding: 5px;">
                    <?= $sendEmailModel->subject ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label><?= Html::label($sendEmailModel->getAttributeLabel('body')) ?></label>
                <div class="mb-20" style="border: 1px solid; padding: 5px;">
                    <?= $sendEmailModel->body ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
            'id' => 'cancel-sent-email',
            'class' => 'btn btn-danger btn-with-icon'
        ]) ?>
        <?= Html::submitButton('<span class="btn-icon"><i class="fa fa-envelope"></i></span><span>Send</span>', [
            'class' => 'btn btn-primary btn-with-icon'
        ]) ?>
    </div>
<?php endif; ?>
    <?php \yii\widgets\ActiveForm::end();
else :
    ?>
    <div class="row text-center">
        <p>Your <strong>Direct Line</strong> and/or <strong>Email password</strong> not set.</p>
        <p>Click <?= Html::a('here', ['site/profile'], ['target' => '_blank']) ?> to set this info!</p>
    </div>
<?php endif; ?>
