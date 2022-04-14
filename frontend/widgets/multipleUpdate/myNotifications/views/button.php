<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this \yii\web\View
 * @var string $gridId
 * @var string $pjaxId
*/
?>
<div class="btn-group check_uncheck_btns">
    <?php echo Html::button('Multiple Update', ['class' => 'btn btn-sm btn-default disabled', 'disabled' => 'disabled', 'id' => 'btn-multiple-update']); ?>
    <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu">
        <p>
            <?php echo
            Html::a(
                '<i class="fa fa-check text-success"></i> Make Read',
                null,
                ['class' => 'dropdown-item btn-multiple-update',
                    'data' => [
                        'url' => Url::to(['notifications/multiple-update-read']),
                        'title' => 'Multiple update - Make Read',
                        'is-popup' => false
                    ],
                ]
            )
            ?>
        </p>
    </div>
</div>

<?php
$js = <<<JS
$(document).off('click', '.btn-multiple-update').on('click', '.btn-multiple-update', function(e) {
    e.preventDefault();    
    var ids = $('body').find('#{$gridId}').yiiGridView('getSelectedRows');
    var title = $(this).data('title');
    if (ids.length < 1) {
        createNotifyByObject({title: title, type: "error", text: 'Not selected rows.', hide: true});
        return false;
    }
    var url = $(this).data('url');
    var isPopup = $(this).data('is-popup');
    var multipleUpdateBtn = $('#btn-multiple-update');
    var btnHtml = multipleUpdateBtn.html();
    var pressedBtn = $(this);
    var pressedBtnHtml = pressedBtn.html();
    if (isPopup) {
        var modalId = $(this).data('modal-id');
        var modal = $(modalId);
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'html',
            cache: false,
            data: {ids: ids},
            beforeSend: function () {
                pressedBtn.prop('disabled', 'disabled').addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i>');
                multipleUpdateBtn.html('<i class="fa fa-spinner fa-spin"></i>');
                modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
                modal.find('.modal-title').html(title);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
            },
            error: function (xhr) {                  
                modal.find('.modal-body').html('Error: ' + xhr.responseText);            
            },
            complete: function () {
                multipleUpdateBtn.html(btnHtml);
                pressedBtn.prop('disabled', false).removeClass('disabled').html(pressedBtnHtml);
            }
        });
    } else {
         $.ajax({
             type: 'post',
             url: url,
             dataType: 'json',
             cache: false,
             data: {ids: ids},
             beforeSend: function () {
                 multipleUpdateBtn.html('<i class="fa fa-spinner fa-spin"></i>');
                 pressedBtn.prop('disabled', 'disabled').addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i>');
             },
             success: function (data) {
                if (data.error) {
                    createNotify(title, data.message, 'error');
                } else {
                    createNotify(title, data.message, 'success');
                    pjaxReload({container: "#{$pjaxId}"})
                }
             },
             error: function (xhr) {                  
                 createNotify(title, xhr.responseText, 'error');
             },
             complete: function () {
                 multipleUpdateBtn.html(btnHtml);
                 pressedBtn.prop('disabled', false).removeClass('disabled').html(pressedBtnHtml);
             }
         });
    }
});
JS;
$this->registerJs($js);
