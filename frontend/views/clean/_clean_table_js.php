<?php

use yii\bootstrap4\Modal;
use yii\helpers\Url;

/* @var string $pjaxIdForReload */
/* @var string|null $cleanTableUrl */
/* @var string|null $cleanFormId */
/* @var string|null $cleanBtnId */

$cleanTableUrl = $cleanTableUrl ?? Url::to(['clean/clean-table-ajax']);
$cleanFormId = $cleanFormId ?? 'clean_form';
$cleanBtnId = $cleanBtnId ?? 'js_btn_clean_records';
?>
<?php
    Modal::begin([
        'title' => '<i class="fa fa-info-circle"></i> Info block for cleaner form',
        'id' => 'cleaner_form_popup',
        'size' => Modal::SIZE_DEFAULT
    ]);
    Modal::end();
    ?>

<div id="info_data" style="display: none;">
    <p>The priority group is <strong>strict_date, datetime, date</strong>.</p>
    <p>Additional group - <em>year, month, day, hour</em>.</p>
    <p>If there are several parameters from the priority group, only one will be used - strict_date, datetime, date (arranged by priority).</p>
    <p>If there is at least one parameter from the priority group, the parameters from the additional group are ignored.</p>
    <p>The parameters from the additional group are added - for example: month = 2 day = 15 records older than two and a half months will be deleted.</p>
    <p><i class="fa fa-exclamation-triangle"></i> Warning - records are deleted from database.</p>
</div>

<?php
$js = <<<JS

    let pjaxIdForReload = '#{$pjaxIdForReload}';
    let cleanTableUrl = '{$cleanTableUrl}';
    let cleanFormId = '#{$cleanFormId}';
    let cleanBtnId = '#{$cleanBtnId}';
     
    $(document).on('click', cleanBtnId, function (e) {
        e.stopPropagation();
        e.preventDefault(); 
        
        if(!confirm('Are you sure? The records will be deleted from the database.')) {
            return false;
        }
        
        let btnSubmit = $(this);
        let btnContent = btnSubmit.html();
            
        btnSubmit.html('<i class="fa fa-cog fa-spin"></i>...')
            .addClass('btn-default')
            .prop('disabled', true);
             
        $.ajax({
            url: cleanTableUrl,
            type: 'POST',
            data: $(cleanFormId).serialize(),
            dataType: 'json'    
        })
        .done(function(dataResponse) {        
            if (dataResponse.status > 0) { 
                createNotify('Success', dataResponse.message, 'success');
                if (pjaxIdForReload.length) {
                    pjaxReload({container: pjaxIdForReload});
                }             
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {        
            createNotify('Error', jqXHR.responseText, 'error');
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .always(function(jqXHR, textStatus, errorThrown) {  
            setTimeout(function () {                        
                btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
            }, 2000);
        });           
    });
    
    $(document).on('click', '#js-info_clean_btn', function (e) { 
        e.preventDefault();
    
        let infoData = $('#info_data').html();
        
        $('#cleaner_form_popup .modal-body').html(infoData);
        $('#cleaner_form_popup').modal('show');     
    });
JS;
$this->registerJs($js);
