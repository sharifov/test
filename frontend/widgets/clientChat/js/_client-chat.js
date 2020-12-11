$(document).on('click', '#_cc-access-wg', function () {
    var loading = $(this).data('loading');
    let promise = new Promise( (resolve, reject) => {
        toggleClientChatAccess(!loading ? null : false);
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

    let $btn = $(this);

    let url = $btn.attr('data-ajax-url');
    let ccuaId = $btn.attr('data-ccua-id');
    let accessAction = $btn.attr('data-access-action');

    let actionBtns = $btn.closest('._cc-action').find('._cc-access-action');

    let btnHtml = $btn.html();

    $.ajax({
        url: url,
        type: 'post',
        data: {ccuaId: ccuaId, accessAction: accessAction},
        cache: false,
        dataType: 'json',
        beforeSend: function () {
            $(actionBtns).prop('disabled', 'true').addClass('disabled');
            $btn.html('<i class="fa fa-spin fa-spinner"></i>');
        },
        success: function (data) {
            if (!data.success) {
                createNotify(data.notifyTitle, data.notifyMessage, data.notifyType);
                actionBtns.each(function (i, elem) {
                    $(elem).removeClass('disabled').removeAttr('disabled');
                });
                $btn.html(btnHtml);
            }
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
            actionBtns.each(function (i, elem) {
                $(elem).removeClass('disabled').removeAttr('disabled');
            });
            $btn.html(btnHtml);
        },
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
        case 'accept_transfer':
            if (document.visibilityState == "visible") {
                window.location.href = data.url;
            }
            window.chat.removeRequest(data.chatId, data.userId, data.chatUserAccessId);
            break;
        case 'skip':
        case 'take':
        case 'deleted':
            window.chat.removeRequest(data.chatId, data.userId, data.chatUserAccessId);
            break;
        case 'pending':
            window.chat.addRequest(data.item);
            break;
        case 'reset':
            if (data.items.length) {
                window.chat.db.addBatch(data.items)
                    .then(() => {window.chat.db.sortData(); window.chat.totalItems = parseInt(data.totalItems)})
                    .then(() => {window.chat.displayAllRequests(2)})
                    .then(() => {window.enableTimer(); toggleClientChatAccess(true)});
            } else {
                window.chat.db.removeAll()
                    .then(() => {window.chat.hasNoRequests()});
            }
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