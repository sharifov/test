$(document).on('click', '#_cc-access-wg', function () {
    let promise = new Promise( (resolve, reject) => {
        toggleClientChatAccess();
        resolve();
    });
    promise.then( () => {
        localStorage.setItem('_cc_wg_status', $('._cc-box').hasClass('is-visible'))}
    );
});

function toggleClientChatAccess(status) {

    if (status === true) {
        $('._cc-box').addClass('is-visible');
        $('.fab').addClass('is-visible');
        $('#_cc-access-wg').addClass('is-visible');
    } else if (status === false) {
        $('._cc-box').removeClass('is-visible');
        $('.fab').removeClass('is-visible');
        $('#_cc-access-wg').removeClass('is-visible');
    } else {
        $('._cc-box').toggleClass('is-visible');
        $('.fab').toggleClass('is-visible');
        $('#_cc-access-wg').toggleClass('is-visible');
    }
    window.enableTimer();
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
            createNotify(data.notifyTitle, data.notifyMessage, data.notifyType);
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
    if ((typeof obj !== "object") && !('data' in obj)) {
        console.error('refreshClientChatWidget:: provided param is not object or property data is undefined');
        return false;
    }

    let data = obj.data;

    if (!('command' in data)) {
        console.error('refreshClientChatWidget:: property command is undefined');
        return false;
    }

    switch (data.command) {
        case 'accept':
            if (document.visibilityState == "visible") {
                if (window.name === 'chat') {
                    window.location.href = data.url;
                } else {
                    window.open(data.url);
                }
            }
            $('#_client_chat_access_widget').html(data.html);
            window.enableTimer();
            break;
        case 'skip':
            $('#_client_chat_access_widget').html(data.html);
            window.enableTimer();
            break;
        case 'pending':
            $('#_client_chat_access_widget').html(data.html);
            window.enableTimer();
            break;
        default:
            console.error('refreshClientChatWidget:: unknown command');
            break;
    }
}