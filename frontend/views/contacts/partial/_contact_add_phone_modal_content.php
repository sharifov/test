<?php

/**
 * @var ActiveForm $form
 * @var View $this
 * @var PhoneCreateForm $addPhone
 * @var Client $client
 */

use borales\extensions\phoneInput\PhoneInput;
use common\models\Client;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use src\forms\lead\PhoneCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var Employee $user */
$user = Yii::$app->user->identity;
$addPhone->client_id = $client->id;
?>

<div class="edit-phone-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-add-phone-form',
        'action' => Url::to(['contacts/ajax-add-contact-phone', 'client_id' => $client->id]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['contacts/ajax-add-contact-phone-validation', 'client_id' => $client->id])
    ]); ?>

    <?= $form->errorSummary($addPhone); ?>

    <?= $form->field($addPhone, 'phone', [
        'options' => [
            'class' => 'form-group',
        ],
    ])->widget(PhoneInput::class, [
        'options' => [
            'class' => 'form-control lead-form-input-element',
            'required' => true
        ],
        'jsOptions' => [
            'nationalMode' => false,
            'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ]) ?>

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
                $('#contact-phones').append(data.html);
                $('#modal-sm').modal('hide');
                
                createNotifyByObject({
                    title: 'Phone successfully added',
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
