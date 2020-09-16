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
    let ccuaId = $(this).attr('data-ccua-id');
    let accessAction = $(this).attr('data-access-action');

    let $btn = $(this);

    let btnHtml = $btn.html();

    $.ajax({
        url: url,
        type: 'post',
        data: {ccuaId: ccuaId, accessAction: accessAction},
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
            $('#ccr_'+data.cch_id+'_'+data.user_id).remove();
            decreaseTotalCount();
            break;
        case 'skip':
            $('#ccr_'+data.cch_id+'_'+data.user_id).remove();
            decreaseTotalCount();
            break;
        case 'deleted':
            $('#ccr_'+data.cch_id+'_'+data.user_id).remove();
            decreaseTotalCount();
            break;
        case 'pending':
            increaseTotalCount();
            if ('isChatInTransfer' in data && data.isChatInTransfer) {
                $('#_client_chat_access_widget ._cc-box-body').prepend(data.html);
            } else {
                $('#_client_chat_access_widget ._cc-box-body').append(data.html);
            }
            window.enableTimer();
            openWidget();
            break;
        case 'reset':
            $('#_client_chat_access_widget').html(data.html);
            window.enableTimer();
            break;
        default:
            console.error('refreshClientChatWidget:: unknown command');
            break;
    }
}

function openWidget() {
    $('#_client_chat_access_widget ._cc-box').addClass('is-visible');
    $('#_cc-access-wg').addClass('is-visible');
}

function closeWidget() {
    $('#_client_chat_access_widget ._cc-box').removeClass('is-visible');
    $('#_cc-access-wg').removeClass('is-visible');
}

function increaseTotalCount() {
    let accessWg = $('#_cc-access-wg');
    let boxHeader = $('._cc-box-header');
    let totalCount = parseInt(accessWg.attr('total-items'));
    let circleWrapper = $('#_circle_wrapper');
    if (totalCount <= 0) {
        $('#_client_chat_access_widget ._cc-box-body').html('');
    }
    accessWg.attr('total-items', totalCount+1).removeClass('inactive');
    $('._cc_total_request_wrapper', accessWg).html(totalCount+1);
    boxHeader.addClass('active');
    circleWrapper.addClass('active');
}

function decreaseTotalCount() {
    let accessWg = $('#_cc-access-wg');
    let boxHeader = $('._cc-box-header');
    let circleWrapper = $('#_circle_wrapper');
    let totalCount = parseInt(accessWg.attr('total-items'))-1;
    accessWg.attr('total-items', totalCount);
    $('._cc_total_request_wrapper', accessWg).html(totalCount);

    if (totalCount <= 0) {
        accessWg.addClass('inactive');
        boxHeader.removeClass('active');
        circleWrapper.removeClass('active');
        $('#_client_chat_access_widget ._cc-box-body').html('<p>You have no active client conversations requests.</p>');
        closeWidget();
    }
}