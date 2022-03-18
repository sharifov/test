<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editPhone PhoneCreateForm
 * @var $lead Lead
 */

use borales\extensions\phoneInput\PhoneInput;
use common\models\ClientPhone;
use common\models\Lead;
use src\forms\lead\PhoneCreateForm;
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
        'id' => 'client-edit-phone-form',
        'action' => Url::to(['lead-view/ajax-edit-client-phone', 'gid' => $lead->gid]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['lead-view/ajax-edit-client-phone-validation', 'gid' => $lead->gid])
    ]); ?>

    <?= $form->errorSummary($editPhone); ?>

    <?php
    $leadAbacDto->formAttribute = 'phone';
    $leadAbacDto->isNewRecord = false;
    /** @abac $leadAbacDto, LeadAbacObject::PHONE_CREATE_FORM, LeadAbacObject::ACTION_VIEW, Phone field view*/
    $view = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::PHONE_CREATE_FORM, LeadAbacObject::ACTION_VIEW);
    /** @abac $leadAbacDto, LeadAbacObject::PHONE_CREATE_FORM, LeadAbacObject::ACTION_EDIT, Phone field edit*/
    $edit = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::PHONE_CREATE_FORM, LeadAbacObject::ACTION_EDIT);
    ?>
        <?= $form->field($editPhone, 'phone', [
            'options' => [
                'class' => 'form-group',
                'hidden' => ($edit ? !$edit : !$view),
            ],
        ])->widget(PhoneInput::class, [
            'options' => [
                'class' => 'form-control lead-form-input-element',
                'id' => 'edit-phone',
                'required' => true,
                'readonly' => !$edit
            ],
            'jsOptions' => [
                'nationalMode' => false,
                'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ]
        ]) ?>
    <?=
    $form->field($editPhone, 'type')->dropDownList(ClientPhone::getPhoneTypeList())
    ?>

    <?=
    $form->field($editPhone, 'id')->hiddenInput()->label(false)->error(false)
    ?>

    <?=
    $form->field($editPhone, 'client_id')->hiddenInput()->label(false)->error(false)
    ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Save phone', [
            'class' => 'btn btn-warning'
        ])
?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-phone-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#client-manage-phone').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                createNotifyByObject({
                    title: 'Phone successfully updated',
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
