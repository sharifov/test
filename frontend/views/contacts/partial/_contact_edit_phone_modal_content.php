<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Client;
use common\models\ClientPhone;
use src\forms\lead\PhoneCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var ActiveForm $form
 * @var View $this
 * @var PhoneCreateForm $editPhone
 * @var Client $client
 */

$user = Yii::$app->user->identity;
?>

<div class="edit-phone-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-edit-phone-form',
        'action' => Url::to(['contacts/ajax-edit-contact-phone', 'client_id' => $client->id]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['contacts/ajax-edit-contact-phone-validation', 'client_id' => $client->id])
    ]); ?>

    <?= $form->errorSummary($editPhone); ?>

    <?php if ($user->isAdmin() || $user->isSuperAdmin()) : ?>
        <?= $form->field($editPhone, 'phone', [
            'options' => [
                'class' => 'form-group',
            ],
        ])->widget(PhoneInput::class, [
            'options' => [
                'class' => 'form-control lead-form-input-element',
                'id' => 'edit-phone',
                'required' => true
            ],
            'jsOptions' => [
                'nationalMode' => false,
                'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ]
        ]) ?>
    <?php endif; ?>

    <?= $form->field($editPhone, 'type')->dropDownList(ClientPhone::getPhoneTypeList()) ?>
    <?= $form->field($editPhone, 'id')->hiddenInput()->label(false)->error(false) ?>
    <?= $form->field($editPhone, 'client_id')->hiddenInput()->label(false)->error(false) ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Edit phone', [
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
    
    let phoneId = '$editPhone->id';
    let phoneRow = $('.phone_row_' + phoneId);
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                phoneRow.replaceWith(data.html);
                $('#modal-sm').modal('hide');
                
                createNotifyByObject({
                    title: 'Phone successfully updated',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            createNotifyByObject({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
