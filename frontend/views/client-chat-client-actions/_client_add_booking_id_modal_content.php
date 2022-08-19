<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var int $chatId
 * @var $addBookingId BookingIdCreateForm
 */

use src\forms\clientChat\BookingIdCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

?>

<div class="add-booking-id-modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'client-add-booking-id-form',
        'action' => Url::to(['/client-chat-client-actions/ajax-add-booking-id', 'id' => $chatId]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => true,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['/client-chat-client-actions/ajax-add-booking-id-validation', 'id' => $chatId])
    ]); ?>

    <?= $form->errorSummary($addBookingId) ?>

    <?= $form->field($addBookingId, 'bookingId')->textInput() ?>
    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-plus"> </i> Add booking id', [
            'class' => 'btn btn-success js-add-booking-id-form-submit'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-add-booking-id-form').on('beforeSubmit', function (e) {
    e.preventDefault();
   let btnSave = $('.js-add-booking-id-form-submit');
    let btnContent = btnSave.html();
    btnSave.html('<i class="fa fa-cog fa-spin"></i> Processing')
        .addClass('btn-default')
        .prop('disabled', true);
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $(document).find('.client-chat-client-form').append(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                createNotifyByObject({
                    title: 'Booking id successfully added',
                    text: data.message,
                    type: 'success'
                });
                
            }
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       },
       error: function (error) {
            createNotifyByObject({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
