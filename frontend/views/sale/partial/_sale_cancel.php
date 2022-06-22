<?php

use src\auth\Auth;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $caseId integer */
/* @var $caseSaleId integer */

$urlCancelSale = Url::to(['/sale/cancel-sale']);
$userCanRefresh = Auth::can('/cases/ajax-refresh-sale-info');
?>

<div class="row">
    <div class="col-md-12 text-center">
        <button type="button" class="btn btn-success confirm-cancel-sale" data-case-sale-id="<?= $caseSaleId ?>" data-case-id="<?= $caseId ?>">
            Confirm
        </button>
    </div>
</div>

<?php
$js = <<<JS
    let userCanRefresh = "$userCanRefresh";
    let caseSaleId = "$caseSaleId";
    $('.confirm-cancel-sale').on('click', function (e) {
        e.preventDefault();
        
        let btn = $(this);
        let modal = btn.parents('.modal');
        let caseSaleId = btn.data('case-sale-id');
        let caseId = btn.data('case-id');
        let loader = $('#preloader');
                
        $.ajax({
            url: "$urlCancelSale",
            type: 'post',
            data: {
                'caseId' : caseId, 
                'caseSaleId' : caseSaleId
            }, 
            dataType: "json",    
            beforeSend: function () {
                btn.attr('disabled', true).find('i').toggleClass('fa-spin');
                loader.removeClass('d-none');
            },
            success: function (data) {
                if (data.error) {
                   createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: data.message,
                        hide: true
                    }); 
                } else {
                    createNotifyByObject({
                        title: "Success",
                        type: "success",
                        text: 'Successfully canceled',
                        hide: true
                    });
                    
                    if(userCanRefresh){
                        $(".refresh-fr-0[data-case-sale-id='" + caseSaleId +"']").trigger('click');
                    }
                }
            },
            error: function () {
                createNotifyByObject({
                    title: "Error",
                    type: "error",
                    text: "Internal Server Error. Try again letter.",
                    hide: true
                });
            },
            complete: function () {
                btn.removeAttr('disabled').find('i').toggleClass('fa-spin');
                loader.addClass('d-none');
                modal.modal('hide');
            }
        });
    });
JS;

$this->registerJs($js);
