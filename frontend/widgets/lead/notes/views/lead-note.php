<?php
$openModalNoteUrl = \yii\helpers\Url::to(['/lead/get-lead-notes']);
?>
    <div>
        <a href="javascript:;" class="js-lead-note" data-id="<?= $leadID ?>">
            Notes
        </a>
    </div>
<?php
$jsCode = <<<JS
 var openModalNoteUrl = '$openModalNoteUrl';
 $(document).on('click', '.js-lead-note', function(){
        let leadId = $(this).data('id');
        let modal = $('#modal-md');
        let noteUrl = openModalNoteUrl + '?leadId=' + leadId;
        $('#modal-md-label').html('Lead Notes: ' + leadId);
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(noteUrl, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal('show');
            }
        });
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);