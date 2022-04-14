<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editEmail EmailCreateForm
 * @var $lead Lead
 */

use common\models\ClientEmail;
use common\models\Lead;
use src\forms\lead\EmailCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\dto\LeadAbacDto;
use src\auth\Auth;

$leadAbacDto = new LeadAbacDto($lead, Auth::id())
?>

<div class="edit-phone-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-edit-email-form',
        'action' => Url::to(['lead-view/ajax-edit-client-email', 'gid' => $lead->gid]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['lead-view/ajax-edit-client-email-validation', 'gid' => $lead->gid])
    ]); ?>

    <?= $form->errorSummary($editEmail) ?>

    <?php
    $leadAbacDto->formAttribute = 'email';
    $leadAbacDto->isNewRecord = false;
    /** @abac $leadAbacDto, LeadAbacObject::EMAIL_CREATE_FORM, LeadAbacObject::ACTION_VIEW, Email field view*/
    $view = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::EMAIL_CREATE_FORM, LeadAbacObject::ACTION_VIEW);
    /** @abac $leadAbacDto, LeadAbacObject::EMAIL_CREATE_FORM, LeadAbacObject::ACTION_EDIT, Email field edit*/
    $edit = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::EMAIL_CREATE_FORM, LeadAbacObject::ACTION_EDIT);
    ?>
    <?=
        $form->field($editEmail, 'email', [
            'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
            'options' => [
                'class' => 'form-group',
                'hidden' => ($edit ? !$edit : !$view),
            ]
        ])->textInput([
            'class' => 'form-control email lead-form-input-element',
            'type' => 'email',
            'required' => true,
            'readonly' => !$edit
        ])
        ?>

    <?=
    $form->field($editEmail, 'type')->dropDownList(ClientEmail::EMAIL_TYPE)
    ?>

    <?=
    $form->field($editEmail, 'id')->hiddenInput()->label(false)->error(false)
    ?>

    <?=
    $form->field($editEmail, 'client_id')->hiddenInput()->label(false)->error(false)
    ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Save email', [
            'class' => 'btn btn-warning'
        ])
?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-email-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#client-manage-email').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                createNotifyByObject({
                    title: 'Email successfully updated',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            if(error.status == 403) {
                createNotifyByObject({
                    title: error.statusText,
                    text: error.responseText,
                    type: 'warning'                
                });
            } else {                
                createNotifyByObject({
                    title: 'Error',
                    text: 'Internal Server Error. Try again letter.',
                    type: 'error'                
                });
            }
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
