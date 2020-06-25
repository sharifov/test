$(document).on('click', '#_cc-access-wg', function () {
    toggleClientChatAccess();
});

function toggleClientChatAccess() {
    $('._cc-box').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
}

$(document).on('click', '._cc-access-action', function (e) {
    e.preventDefault();

    let url = $(this).attr('data-ajax-url');
    let cchId = $(this).attr('data-cch-id');
    let accessAction = $(this).attr('data-access-action');

    let $btn = $(this);

    let btnHtml = $btn.html();

    $.ajax({
        url: url,
        type: 'post',
        data: {cchId: cchId, accessAction: accessAction},
        cache: false,
        dataType: 'json',
        beforeSend: function () {
            $btn.prop('disabled', 'true').addClass('disabled').html('<i class="fa fa-spin fa-spinner"></i>');
        },
        success: function (data) {
            if (data.success) {
                $btn.closest('._cc-box-item-wrapper').remove();
                return false;
            }
            createNotify('Error', data.message, 'error');
        },
        error: function (xhr) {
            console.log(xhr);
        },
        complete: function () {
            $btn.html(btnHtml).removeClass('disabled').removeAttr('disabled');
        }
    })
})

function refreshClientChatWidget(obj) {
    console.log(obj);

    if ((typeof obj !== "object") && !('data' in obj)) {
        console.error('refreshClientChatWidget:: provided param is not object or property data is undefined');
        return false;
    }

    let data = obj.data;

    if (!('command' in data)) {
        console.error('refreshClientChatWidget:: property command is undefined');
        return false;
    }

    var id = '#ccr_'+data.cch_id + '_' + data.user_id;
    switch (data.command) {
        case 'accept':

            $(id).remove();
            pjaxReload({container: '#client-chat-box-pjax'});
            if (document.visibilityState == "visible") {
                window.open(data.url);
            }
            break;
        case 'skip':
            break;
        case 'pending':
            pjaxReload({container: '#client-chat-box-pjax'});
            break;
        default:
            console.error('refreshClientChatWidget:: unknown command');
            break;
    }
}