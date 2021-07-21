<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $lead Lead
 * @var $addPhone PhoneCreateForm
 */

use borales\extensions\phoneInput\PhoneInput;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use sales\forms\lead\PhoneCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\dto\LeadAbacDto;
use sales\auth\Auth;

$addPhone->client_id = $lead->client_id;
$leadAbacDto = new LeadAbacDto($lead, Auth::id())
?>

<div class="edit-phone-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-add-phone-form',
        'action' => Url::to(['lead-view/ajax-add-client-phone', 'gid' => $lead->gid]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => true,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['lead-view/ajax-add-client-phone-validation', 'gid' => $lead->gid])
    ]); ?>

    <?= $form->errorSummary($addPhone); ?>

    <?php /** @abac $leadAbacDto, LeadAbacObject::UI_FIELD_PHONE_FROM_ADD_PHONE, LeadAbacObject::ACTION_ACCESS, Access Field Phone in form Add Phone*/ ?>
    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_FIELD_PHONE_FORM_ADD_PHONE, LeadAbacObject::ACTION_CREATE)) : ?>
            <?= $form->field($addPhone, 'phone', [
                'options' => [
                    'class' => 'form-group',
                ],
            ])->widget(PhoneInput::class, [
                'options' => [
                    'class' => 'form-control lead-form-input-element',
                    'required' => true,
                    'onkeyup' => 'var value = $(this).val();$(this).val(value.replace(/[^0-9\+]+/g, ""));'
                ],
                'jsOptions' => [
                    'nationalMode' => false,
                    'preferredCountries' => ['us'],
                    'customContainer' => 'intl-tel-input'
                ]
            ]) ?>
    <?php endif; ?>

        <?=
        $form->field($addPhone, 'type')->dropDownList(ClientPhone::getPhoneTypeList())
        ?>
        <div class="text-center">
            <?= Html::submitButton('<i class="fa fa-plus"></i> Add Phone', [
                'class' => 'btn btn-success'
            ])
?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-add-phone-form').on('beforeSubmit', function (e) {
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
                
                new PNotify({
                    title: 'Phone successfully added',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            if(error.status == 403) {
                new PNotify({
                    title: error.statusText,
                    text: error.responseText,
                    type: 'warning'                
                });
            } else {                
                new PNotify({
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
