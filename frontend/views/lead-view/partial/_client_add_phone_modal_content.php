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

/** @var Employee $user */
$user = Yii::$app->user->identity;
$addPhone->client_id = $lead->client_id;
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

    <?php if ($lead->isOwner($user->id) || $user->isAnySupervision() || $user->isAdmin() || $user->isSuperAdmin()) : ?>
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
            new PNotify({
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
