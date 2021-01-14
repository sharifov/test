<?php

/**
 * @var string|null $caseBookingId
 * @var string|null $saleBookingId
 * @var int $caseId
 * @var int $saleId
 */

use yii\helpers\Html;
use yii\helpers\Url;

$updateBookingIdUrl = Url::to('/cases/update-booking-id-by-sale');
?>
<div class="row">
    <div class="col-md-12">
        <p><b>Would you like to update Case Booking Id?</b></p>

        <p>
            <span>Current: <?= $caseBookingId ? Html::encode($caseBookingId) : '(not set)' ?></span><br>
            <span>New: <?= $saleBookingId ? Html::encode($saleBookingId) : '(not set)' ?></span>
        </p>

        <div class="text-center">
            <?= Html::button('<i class="fa fa-check"></i> Update', ['class' => 'btn btn-success', 'id' => 'updateCaseBookingId']) ?>
            <?= Html::button('Keep current', ['class' => 'btn btn-warning keepCurrent', 'data-dismiss' => 'modal']) ?>
        </div>
    </div>
</div>
<script>
    $("#updateCaseBookingId").on('click', function (e) {
        e.preventDefault();
        let btn = $(this); 
        let btnHtml = btn.html();
        let keepCurrentBtn = $('.keepCurrent');
        let modal = $('#modalCaseSm');
        $.ajax({
            type: 'post',
            url: '<?= $updateBookingIdUrl ?>',
            cache: false,
            dataType: 'json',
            data: {caseId: '<?= Html::encode($caseId) ?>', saleId: '<?= Html::encode($saleId) ?>'},
            beforeSend: function () {
                btn.html('<span class="spinner-border spinner-border-sm"></span> Update').prop('disabled', true);
                keepCurrentBtn.prop('disabled', true);
            },
            complete: function () {
                keepCurrentBtn.prop('disabled', false);
                btn.html(btnHtml).prop('disabled', false);
            },
            success: function (response) {
                if (response.error) {
                    createNotify('Error', response.message, 'error');
                } else {
                    createNotify('Success', response.message, 'success');
                    modal.modal('hide');
                    $('#caseBookingId').html(response.newCaseBookingId);
                }
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            }
        })
    });
</script>

