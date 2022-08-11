<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= Html::button('<i class="fa fa-info-circle"></i> Sale Details', ['class' => 'btn btn-info', 'id' => 'sale-detail-view', 'data-url' => Url::to(['lead-sale/ajax-sale-detail', 'leadId' => $leadId])])?>
<?php
$jsCode = <<<JS
 $(document).on('click', '#sale-detail-view', function(){
        let url = $(this).data('url');
        $('#preloader').removeClass('d-none');
        let modal = $('#modal-md');
        modal.find('.modal-title').html('Sale Details.   Sale ID: ' + {$saleId});
        $.ajax({
            type: 'get',
            url: url,
            success: function (data) {
                $('#preloader').addClass('d-none');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
                $('#preloader').addClass('d-none');
                createNotify('Error', error.statusText, 'error');
            }
        });
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
