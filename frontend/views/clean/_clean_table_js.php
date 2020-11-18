<?php

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
JS;
$this->registerJs($js);
