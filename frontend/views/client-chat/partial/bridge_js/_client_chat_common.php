<?php

use sales\auth\Auth;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\dashboard\ReadUnreadFilter;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use sales\model\clientChat\entity\ClientChat;

/* @var yii\web\View $this */
/* @var ClientChat|null $clientChat */
/* @var FilterForm $filter */

$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';
$clientChatId = $clientChat ? $clientChat->cch_id : 0;
$clientChatOwnerId = ($clientChat && $clientChat->cch_owner_user_id) ? $clientChat->cch_owner_user_id : 0;
$readAll = ReadUnreadFilter::ALL;

$rcUrl = Yii::$app->rchat->host . '/home';
$loadChannelsUrl = Url::to('/client-chat/index');
$clientChatInfoUrl = Url::toRoute('/client-chat/info');
$clientChatNoteUrl = Url::toRoute('/client-chat/note');
$clintChatDataIUrl = Url::toRoute('/client-chat/ajax-data-info');
$clientChatCloseUrl = Url::toRoute('/client-chat/ajax-close');
$chatHistoryUrl = Url::toRoute('/client-chat/ajax-history');
$chatTransferUrl = Url::toRoute('/client-chat/ajax-transfer-view');
$chatReopenUrl = Url::toRoute('/client-chat/ajax-reopen-chat');
$chatCancelTransferUrl = Url::toRoute('/client-chat/ajax-cancel-transfer');
$chatSendOfferListUrl = Url::toRoute('/client-chat/send-offer-list');
$chatSendOfferPreviewUrl = Url::toRoute('/client-chat/send-offer-preview');
$chatSendOfferGenerateUrl = Url::toRoute('/client-chat/send-offer-generate');
$chatSendOfferUrl = Url::toRoute('/client-chat/send-offer');
$chatHoldUrl = Url::toRoute('/client-chat/ajax-hold-view');
$chatUnHoldUrl = Url::toRoute('/client-chat/ajax-un-hold');
$clientChatResetUnreadMessageUrl = Url::toRoute(['/client-chat/reset-unread-message']);
$clientChatAddActiveConnectionUrl = Url::toRoute(['/client-chat/add-active-connection']);
$clientChatRemoveFromActiveConnectionUrl = Url::toRoute(['/client-chat/remove-active-connection']);
$clientChatTakeUrl = Url::toRoute(['/client-chat/ajax-take']);
$clientChatReturnUrl = Url::toRoute(['/client-chat/ajax-return']);
$clientChatCouchNoteUrl = Url::toRoute(['/client-chat/ajax-couch-note']);
$clientChatCouchNoteViewUrl = Url::toRoute(['/client-chat/ajax-couch-note-view']);
$clientChatReloadChatUrl = Url::toRoute(['/client-chat/ajax-reload-chat']);
$moveOfferUrl = Url::to(['/client-chat/move-offer']);
$discardUnreadMessageUrl = Url::to(['/client-chat/discard-unread-messages']);
$selectAllUrl = Url::to(['client-chat/index']);
$clientChatMultipleUpdate = Url::to(['client-chat/ajax-multiple-update']);
$cannedResponseSendMessageUrl = Url::to(['client-chat/ajax-send-canned-response']);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

$this->registerJsFile('/js/moment.min.js', [
    'position' => View::POS_HEAD,
    'depends' => [
        JqueryAsset::class,
    ],
]);

$js = <<<JS
let currentChatId = {$clientChatId};
let currentChatOwnerId = {$clientChatOwnerId};

window.name = 'chat';

let spinnerContent = '<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>';
let loaderIframe = '<div id="_cc-load">' + spinnerContent + '</div>';  

$(document).ready( function () {
    let clientChatId = {$clientChatId};

    $('textarea.canned-response').devbridgeAutocomplete({
        noCache: true,
        serviceUrl: '/client-chat/ajax-canned-response',
        deferRequestBy: 1300,
        minChars: 3,
        params: {chatId: clientChatId},
        delimiter: '/',
        orientation: 'top',
        showNoSuggestionNotice: true,
        triggerSelectOnValidInput: false,
        transformResult: function(response) {
            response = JSON.parse(response);
            if (response.message) {
                createNotify('Error', response.message, 'error');
                $('#send-canned-response').show();
                $('#loading-canned-response').hide();
                return {suggestions: []};
            } else {
                return {
                    suggestions: $.map(response.data, function(dataItem) {
                        return { value: dataItem.message, data: dataItem.headline_message };
                    })
                };
            }
        },
        formatResult: function (suggestion, currentValue) {
            return suggestion.data;
        },
        onSearchStart: function (params) {
            if (!$(this).val().includes("/")) {
                return false;
            }
            params['chatId'] = $(this).attr('data-chat-id');
            $('#send-canned-response').hide();
            $('#loading-canned-response').show();
        },
        onSearchComplete: function () {
            $('#send-canned-response').show();
            $('#loading-canned-response').hide();
        },
        onSelect: function (suggestion) {
            let searchValue = '/'+suggestion.value;
            let curVal = $(this).val();
            $(this).val(curVal.replace(searchValue, suggestion.value));
        },
        onSearchError: function (query, jqXHR, textStatus, errorThrown) {
            createNotify('Error', jqXHR.statusText, 'error');
            $('#send-canned-response').show();
            $('#loading-canned-response').hide();
        }
    });
    
    $('#send-canned-response').on('click', function () {
        let message = $('#canned-response').val();
        let chatId = $('#canned-response').attr('data-chat-id');
        
        $.ajax({
            type: 'post',
            url: '{$cannedResponseSendMessageUrl}',
            dataType: 'json',
            cache: false,
            data: {chatId: chatId, message: message},
            beforeSend: function () {
                $('#send-canned-response').hide();
                $('#loading-canned-response').show();
            },
            success: function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                } else {
                    $('#canned-response').val('');
                }
            },
            complete: function () {
                $('#send-canned-response').show();
                $('#loading-canned-response').hide();  
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            },
        })
    });
    
    $(window).on("beforeunload", function() { 
        localStorage.removeItem('activeChatId');
        window.name = '';
    })
    
    let activeChatId = clientChatId;
    let params = new URLSearchParams(window.location.search);
    let chatId = params.get('chid'); 
    
    if (activeChatId) {
        localStorage.setItem('activeChatId', activeChatId);
    }
            
    document.addEventListener("visibilitychange", function () {
        if (window.name === 'chat') {            
            if (activeChatId == chatId) {
                $.post('{$discardUnreadMessageUrl}', {cchId: activeChatId});
            }
        }
    })
});

$(document).ready( function () {      
    updateLastMessageTime()
    $('#pjax-client-chat-channel-list').on('pjax:success', function() {
        updateLastMessageTime()
    }) 
    let interval = 60;
    setInterval(function () {
         $('._cc-item-last-message-time[data-moment]').each( function (i, elem) {
             let seconds = +($(elem).attr('data-moment')) + interval;
             $(elem).attr('data-moment', seconds);
             $(elem).html(moment.duration(-seconds, 'seconds').humanize(true));
         });
    }, interval*1000); 
});

function updateLastMessageTime() {
    $('._cc-item-last-message-time[data-moment]').each( function (i, elem) {
        $(elem).html(moment.duration(-$(elem).data('moment'), 'seconds').humanize(true));
    });
}

$(document).on('click', '#btn-load-channels', function (e) {
    
    e.preventDefault();
    
    let page = $(this).attr('data-page');
    let btn = $(this);
    
    let btnCurrentText = btn.html();
    
    let params = new URLSearchParams(window.location.search);

    let urlParams = window.getClientChatLoadMoreUrl('{$filter->getId()}', '{$filter->formName()}');
    let url = '{$loadChannelsUrl}?' + urlParams + '&loadingChannels=1' + '&page=' + page;
    $.ajax({
        type: 'get',
        url: url,
        dataType: 'json',
        cache: false,
        // data: {loadingChannels: 1, channelId: params.get('channelId') | selectedChannel},
        beforeSend: function () {
            btn.html('<i class="fa fa-spin fa-spinner"></i> Loading...').prop('disabled', true).addClass('disabled');
        },
        success: function (data) {
            if (data.html) {
                $('._cc-list-wrapper').append(data.html);
                refreshUserSelectedState();
            }
            if (data.isFullList) {
                btn.html('All conversations are loaded');
            } else {
                let txt = '<i class="fa fa-angle-double-down"> </i> Load more (<span>' + data.moreCount + '</span>)';
                btn.html(txt).removeAttr('disabled').removeClass('disabled').attr('data-page', data.page);
            }
            window.history.replaceState({}, '', '{$loadChannelsUrl}?' + urlParams + '&page=' + (data.page - 1));
        },
        error: function (xhr) {
            btn.html(btnCurrentText);
        },
    });
});

// if ($('#_rc-iframe-wrapper').find('._rc-iframe').length) {
//     let iframe = $($('#_rc-iframe-wrapper').find('._rc-iframe')[0]);
//     let windowHeight = $(window)[0].innerHeight;
//     let offsetTop = $("#_rc-iframe-wrapper").offset().top;
//     let iframeHeight = windowHeight - offsetTop - 20;
//     $(iframe).css('height', iframeHeight+'px');
// }

$(document).on('click', '.cc_btn_group_filter', function () {
    let newValue = $(this).attr('data-group-id');
    let groupInput = $(document).find('#{$filter->getGroupInputId()}');
    groupInput.val(newValue);
    window.updateClientChatFilter('{$filter->getId()}', '{$filter->formName()}', '{$loadChannelsUrl}');
    sessionStorage.selectedChats = '{}';
    refreshUserSelectedState();
    $('#canned-response-wrap').addClass('disabled');
    $('#couch_note_box').html('');
});

$(document).on('click', '#{$filter->getReadUnreadInputId()}', function () {
    window.updateClientChatFilter('{$filter->getId()}', '{$filter->formName()}', '{$loadChannelsUrl}');
});

function clientChatResetUnreadMessageCounter(chatId) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '{$clientChatResetUnreadMessageUrl}',
        data: {
            'chatId': chatId
        }
    })
    .done(function(data) {
        if (data.error) {
            createNotify('Reset unread message', data.message, 'error');
            return;
        }
        $(document).find("._cc-chat-unread-message").find("[data-cch-id='" + chatId + "']").html(''); 
    })
    .fail(function(xhr) {
      createNotify('Reset unread message', xhr.responseText, 'error');
    })
    ;
}

function addChatToActiveConnection() {
    if (!currentChatId || currentChatOwnerId != userId || !window.socketConnectionId) {
        return;
    }
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '{$clientChatAddActiveConnectionUrl}',
        data: {
            'chatId': currentChatId,
            'connectionId': window.socketConnectionId
        }
    })
    .done(function(data) {
        // if (data.error) {
        //     createNotify('Add chat to active connection ', data.message, 'error');
        // }
    })
    .fail(function(xhr) {
        // createNotify('Add chat to active connection ', xhr.responseText, 'error');
    })
    ;
}
window.removeChatFromActiveConnection = function () {
    if (!currentChatId || currentChatOwnerId != userId || !window.socketConnectionId) {
        return;
    }
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '{$clientChatRemoveFromActiveConnectionUrl}',
        data: {
            'chatId': currentChatId,
            'connectionId': window.socketConnectionId
        }
    })
    .done(function(data) {
        // if (data.error) {
        //     createNotify('Add chat to active connection ', data.message, 'error');
        // }
    })
    .fail(function(xhr) {
        // createNotify('Add chat to active connection ', xhr.responseText, 'error');
    })
    ;
    currentChatId = 0;
    currentChatOwnerId = 0;
};

window.loadClientChatData = function (cch_id, data, ref) {
    
    let isClosed = data.isClosed;
    let iframeWrapperEl = $("#_rc-iframe-wrapper");
        
    iframeWrapperEl.find('._rc-iframe').hide();
    $('._cc-list-item').removeClass('_cc_active');
    $(ref).addClass('_cc_active');
    
    iframeWrapperEl.find('#_cc-load').remove();
    iframeWrapperEl.append(loaderIframe);
    
    let chatEl = $('#_rc-' + cch_id);
    let chatIsShowInput = parseInt(chatEl.data('isShowInput'), 10);
    
    if (!chatEl.length) {
        $('#_rc-iframe-wrapper').append(data.iframe);
    } else if (chatEl.length && chatIsShowInput !== data.isShowInput) {
        chatEl.attr('src', data.iframeSrc);
    }
    
    $('#couch_note_box').html('');
    if (!isClosed) {
        window.refreshCouchNote(cch_id);
    }
    
    if (data.isShowInput) {
        $('#canned-response-wrap').removeClass('disabled');
        $('#canned-response').attr('data-chat-id', cch_id).val('');
    } else {
        $('#canned-response-wrap').addClass('disabled');
    }
    
    let params = new URLSearchParams(window.location.search);
    params.set('chid', cch_id);
    window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
    
    localStorage.setItem('activeChatId', cch_id);
    
    chatEl.show();
    window.removeCcLoadFromIframe();
}

$(document).on('click', '._cc-list-item', function () {
    $('#cc-dialogs-wrapper').append(loaderIframe); 
    let iframeWrapperEl = $("#_rc-iframe-wrapper");
    iframeWrapperEl.find('#_cc-load').remove();
    iframeWrapperEl.append(loaderIframe);

    let cch_id = $(this).attr('data-cch-id');
    currentChatId = cch_id;
    let ownerId = $(this).attr('data-owner-id');
    currentChatOwnerId = ownerId;
    
    if (ownerId === userId) {
        addChatToActiveConnection();    
    }
        
    if ($(this).hasClass('_cc_active')) {
        $('#cc-dialogs-wrapper #_cc-load').remove(); 
        iframeWrapperEl.find('#_cc-load').remove();
        return false;
    }
    
    let ref = this;
    window.refreshChatInfo(cch_id, loadClientChatData, ref);
    
    setTimeout(function () {
        $('#cc-dialogs-wrapper #_cc-load').remove();
        $("#_rc-iframe-wrapper").find('#_cc-load').remove();
    }, 2000);
});

$(document).on('click', '.cc_cancel_transfer', function (e) {
    e.preventDefault();
    let btn = $(this);
    let cchId = btn.attr('data-cch-id');
    let btnHtml = btn.html();
    
    if (confirm('Confirm transfer cancellation')) {
        $.ajax({
            type: 'post',
            url: '{$chatCancelTransferUrl}',
            dataType: 'json',
            cache: false,
            data: {cchId: cchId},
            beforeSend: function () {
                btn.html('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                } else {                    
                    createNotify('Success', data.message, 'success');
                }
            },
            complete: function () {
                btn.html(btnHtml);
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            }
        });
    }
});

$(document).on('click', '.cc_full_info', function (e) {
    e.preventDefault();
    let cchId = $(this).attr('data-cch-id');
    let modal = $('#modal-lg');
    
    $.ajax({
        type: 'post',
        url: '{$clintChatDataIUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},
        beforeSend: function () {
            modal.find('.modal-body').html(spinnerContent);
            modal.find('.modal-title').html('Client Chat Info');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {                  
            modal.find('.modal-body').html('Error: ' + xhr.responseText);
            //createNotify('Error', xhr.responseText, 'error');
        },
    });
});

$(document).on('click', '.cc_transfer', function (e) {
    e.preventDefault();
    let cchId = $(this).attr('data-cch-id');
    let modal = $('#modal-sm');
    
    $.ajax({
        type: 'post',
        url: '{$chatTransferUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},
        beforeSend: function () {
            modal.find('.modal-body').html(spinnerContent);
            modal.find('.modal-title').html('Client Chat Transfer');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {
            modal.modal('hide');
            createNotify('Error', xhr.responseText, 'error');
        },
    });
});

$(document).on('click', '.cc_reopen', function (e) {
    e.preventDefault();
    let btn = $(this);
    let cchId = btn.attr('data-cch-id');
    
    let btnIcon = btn.find('i');
    
    if (confirm('Confirm reopen action...')) {
        $.ajax({
            type: 'post',
            url: '{$chatReopenUrl}',
            dataType: 'json',
            cache: false,
            data: {chatId: cchId},
            beforeSend: function () {
                btn.find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                } else {
                    createNotify('Success', 'Chat reopened successfully', 'success');
                }
            },
            complete: function () {
                btn.find('i').replaceWith(btnIcon);
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            },
        });
    }
});

$(document).on('click', '.cc_close', function (e) {
    e.preventDefault();
    let btn = $(this);
    let cchId = btn.attr('data-cch-id');
    // let btnHtml = btn.html();
    let modal = $('#modal-sm');
    
    $.ajax({
        type: 'post',
        url: '{$clientChatCloseUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},
        beforeSend: function () {
            // btn.html('<i class="fa fa-spin fa-spinner"></i>');
            modal.find('.modal-body').html(spinnerContent);
            modal.find('.modal-title').html('Client Chat Close Chat');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
            // if (data.error) {
            //     createNotify('Error', data.message, 'error');
            // } else {
            //     refreshChatPage(cchId, data.tab);
            //     createNotify('Success', data.message, 'success');
            // }
        },
        // complete: function () {
        //     btn.html(btnHtml);
        // },
        error: function (xhr) {
            modal.modal('hide');
            createNotify('Error', xhr.responseText, 'error');
        }
    });
});

window.removeCcLoadFromIframe = function () {
    $('#_rc-iframe-wrapper').find('#_cc-load').remove();
}

window.getChatHistory = function (cchId) {
    $("#_rc-iframe-wrapper").find('._rc-iframe').hide();
    $("#_rc-iframe-wrapper").find('#_cc-load').remove();
    $("#_rc-iframe-wrapper").append(loaderIframe);
        
    $.post('{$chatHistoryUrl}', {cchId: cchId}, function(data) {
        if (data.indexOf('iframe') !== -1) {
            $('#_rc-'+cchId).remove();
        }
        $("#_rc-iframe-wrapper").append(data);
    });
}

window.refreshChatInfo = function (cch_id, callable, ref) {
    $.ajax({
        type: 'post',
        url: '{$clientChatInfoUrl}',
        dataType: 'json',
        cache: false,
        data: {cch_id: cch_id},
        beforeSend: function () {
            $('#_cc_additional_info_wrapper').append(loaderIframe);
        },
        success: function (data) {
            $('#_client-chat-info').html(data.html);
            $('#_client-chat-note').html(data.noteHtml);
            if (callable) {
                callable(cch_id, data, ref);
            }          
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        },
        complete: function () {
            $('#_cc_additional_info_wrapper #_cc-load').remove();
            $('#cc-dialogs-wrapper #_cc-load').remove(); 
        }
    });
}

window.refreshChatPage = function (cchId) {    
    preReloadChat(cchId);
    refreshChannelList();
    refreshChatInfo(cchId);    
    
    getChatDataPromise(cchId).then(function(chatData) {
        return reloadChat(chatData);
    }).then(function(chatData) {
        return reloadCannedResponse(chatData);
    }).then(function(chatData) {
        return reloadCouchNote(chatData);
    }).catch(function(errorMsg) {
        console.log({error: 'refreshChatPage', msg: errorMsg});
        createNotify('Error', errorMsg, 'error');
    }).finally(function() {
        postReloadChat();
    }); 
    
    setTimeout(function () {
        postReloadChat();
    }, 3000);   
}

window.refreshChannelList = function() {  
    if ($('#pjax-client-chat-channel-list').length) {
        pjaxReload({container: '#pjax-client-chat-channel-list'});
    }
}

window.getChatDataPromise = function (cchId) {  
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: '{$clientChatReloadChatUrl}',
            type: 'post',
            data: {cchId: cchId},
            dataType: 'json'    
        })
        .done(function(dataResponse) {                
            if (dataResponse.status) {
                resolve(dataResponse);
            } else if (dataResponse.status === 0 && dataResponse.message.length) {
                reject(new Error(dataResponse.message));
            } else {
                reject(new Error('Error. Please see logs'));
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            reject(new Error(jqXHR.responseText));
        })
        .always(function(jqXHR, textStatus, errorThrown) {}); 
    });    
}

reloadCannedResponse = function(chatData) {
    return new Promise(function(resolve, reject) {
        if (chatData.isShowInput) {
            $('#canned-response-wrap').removeClass('disabled');
            $('#canned-response').attr('data-chat-id', chatData.cchId).val('');
        } else {
            $('#canned-response-wrap').addClass('disabled');
        } 
        resolve(chatData);                        
    }); 
}

reloadCouchNote = function(chatData) {
    return new Promise(function(resolve, reject) {        
        window.refreshCouchNote(chatData.cchId);         
        resolve(chatData);                          
    }); 
}

reloadChat = function(chatData) {
    return new Promise(function(resolve, reject) {
        $('#_rc-iframe-wrapper').append(chatData.iframe);  
        resolve(chatData);                  
    }); 
}

preReloadChat = function(cchId) {
    $('#page-loader').show();  
    $('#_rc-'+cchId).remove();
    
    let iframeWrapperEl = $("#_rc-iframe-wrapper");
    iframeWrapperEl.find('._rc-iframe').hide();
    iframeWrapperEl.find('#_cc-load').remove();
    iframeWrapperEl.append(loaderIframe);
}

postReloadChat = function() {
    $('#page-loader').hide();
}

window.refreshCouchNote = function (cch_id) {    
    $('#couch_note_box').html('');    
    $.ajax({
        url: '{$clientChatCouchNoteViewUrl}',
        type: 'POST',
        data: {cch_id: cch_id},
        dataType: 'json'    
    })
    .done(function(dataResponse) {                
        if (dataResponse.status > 0 && dataResponse.html.length) { 
            $('#couch_note_box').html(dataResponse.html);
        } else if (dataResponse.status === 0 && dataResponse.message.length) {
            console.log(dataResponse.message);
        } 
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR.responseText);
    })
    .always(function(jqXHR, textStatus, errorThrown) {}); 
}

$(document).on('click', '.chat-offer', function(e) {
    e.preventDefault();
    let chatId = $(this).attr('data-chat-id');
    let leadId = $(this).attr('data-lead-id');
    let modal = $('#modal-lg');
    
    modal.find('.modal-body').html(spinnerContent);
    modal.find('.modal-title').html('Send Offer');
    modal.modal('show');

    $.ajax({
        type: 'post',
        url: '{$chatSendOfferListUrl}',
        data: {chat_id: chatId, lead_id: leadId},
        dataType: 'html'
    })
    .done(function(data) { 
            modal.find('.modal-body').html(data);
    })
    .fail(function () {
            createNotify('Error', 'Server error', 'error');
    });
});

$(document).on('click', '.quotes-uid-chat-generate', function(e) {
    e.preventDefault();
     let chatId = $(this).attr('data-chat-id');
     let leadId = $(this).attr('data-lead-id');
     if (!chatId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
         return;
     }
     if (!leadId) {
         createNotify('Send Offer', 'Not found Lead Id', 'error');
         return;
     }
    
    let quotes = [];
       
    $('input[type=checkbox].quotes-uid:checked').each(function() {
        quotes.push($(this).data('id'));
    });
    
    if (quotes.length < 1) {
        createNotify('Send Offer', 'Not found selected quotes', 'error');
        return false;
    }
    
    let modal = $('#modal-lg');
    modal.find('.modal-body').html(spinnerContent);    
    
     $.ajax({
        type: 'post',
        url: '{$chatSendOfferGenerateUrl}',
        data: {chatId: chatId, leadId: leadId, quotesIds: quotes},
        dataType: 'html'
    })
    .done(function(data) { 
            modal.find('.modal-body').html(data);
    })
    .fail(function () {
            createNotify('Error', 'Server error', 'error');
    });    
        
});

$(document).on('click', '.client-chat-send-offer', function(e) {
    e.preventDefault();
     let chatId = $(this).attr('data-chat-id');
     let leadId = $(this).attr('data-lead-id');
     if (!chatId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
         return;
     }
     if (!leadId) {
         createNotify('Send Offer', 'Not found Lead Id', 'error');
         return;
     }
     
     let modal = $('#modal-lg');
     modal.find('.modal-body').html(spinnerContent);    
    
     $.ajax({
        type: 'post',
        url: '{$chatSendOfferUrl}',
        data: {chatId: chatId, leadId: leadId},
        dataType: 'json'
    })
    .done(function(data) {
        if (data.error) {
            modal.find('.modal-body').html(data.message);
            return false;
        }
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('');
        modal.modal('hide');
    })
    .fail(function () {
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('');
        modal.modal('hide');
        createNotify('Error', 'Server error', 'error');
    }); 
});    
        
    $("#pjax-notes").on("pjax:start", function () {         
        $("#btn-submit-note").prop("disabled", true).addClass("disabled");
        $("#btn-submit-note i").attr("class", "fa fa-cog fa-spin fa-fw");
    });
    
    $("#pjax-notes").on("pjax:end", function () {           
        $("#btn-submit-note").prop("disabled", false).removeClass("disabled");
        $("#btn-submit-note i").attr("class", "fa fa-plus");        
    });

$(document).on('click', '.create_lead', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-link');
    let modal = $('#modal-md');
    let modalTitle = 'Create Lead';
    $.ajax({
        type: 'post',
        url: url,
        data: {},
        dataType: 'html',
        beforeSend: function () {
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.find('.modal-title').html(modalTitle);
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
            modal.find('.modal-title').html(modalTitle);
            $('#preloader').addClass('d-none');
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                createNotify('Error', xhr.responseText, 'error');
            } else {
                createNotify('Error', 'Internal Server Error. Try again letter.', 'error');
            }
            setTimeout(function () {
                $('#modal-md').modal('hide');
            }, 300)
        },
    })

});
$(document).on('click', '.create_case', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-link');
    let modal = $('#modal-md');
    let modalTitle = 'Create Case';
    $.ajax({
        type: 'post',
        url: url,
        data: {},
        dataType: 'html',
        beforeSend: function () {
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.find('.modal-title').html(modalTitle);
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
            modal.find('.modal-title').html(modalTitle);
            $('#preloader').addClass('d-none');
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                createNotify('Error', xhr.responseText, 'error');
            } else {
                createNotify('Error', 'Internal Server Error. Try again letter.', 'error');
            }
            setTimeout(function () {
                $('#modal-md').modal('hide');
            }, 300)
        },
    })

});

$(document).on('click', '.btn_toggle_form', function (e) {
    $("#clientchatnote-ccn_note").val('');
    let modal = $('#add-note-model');
    modal.modal('show');
});

$(document).on('click', '#btn-submit-note', function (e) {    
    if ($("#clientchatnote-ccn_note").val() !== ''){
       let modal = $('#add-note-model');
        modal.modal('hide');
    }    
});

$(document).on('click', '.btn_move_offer', function(e) {
  e.preventDefault();
  let btn = $(this);
  let leadId = $(this).attr('data-lead-id');
  let chatId = $(this).attr('data-chat-id');
  let captureKey = $(this).attr('data-capture-key');
  let type = $(this).attr('data-type');
  btn.attr('disabled', true);
  btn.removeClass('fa-arrow-up');
  btn.addClass('fa-spinner');
  btn.removeClass('btn-success');
  btn.addClass('btn-default');
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: '{$moveOfferUrl}',
    data: {
        leadId: leadId,
        chatId: chatId,
        captureKey: captureKey,
        type: type
    }
  })
  .done(function(data) {
    $(document).find('.send-offer-container').html(data.view);
    if (data.error) {
        createNotify('Move offer', data.error, 'error');
    }
  })
  .fail(function (xhr, textStatus, errorThrown) {
      createNotify('Move offer', xhr.responseText, 'error');
  })
  .always(function() {
    btn.attr('disabled', false);
    btn.removeClass('fa-spinner');
    btn.addClass('fa-arrow-up');
    btn.removeClass('btn-default');
    btn.addClass('btn-success');
  });
});

$(document).on('click', '.cc_hold', function (e) {
    e.preventDefault();
    
    let btnHold = $(this);
    let btnContent = btnHold.html();
        
    btnHold.html('<i class="fa fa-cog fa-spin"></i> Loading...')
        .addClass('btn-default')
        .prop('disabled', true);
    
    let cchId = btnHold.attr('data-cch-id');
    let modal = $('#modal-sm');
    modal.find('.modal-body').html(spinnerContent);
    modal.modal('show');
                
    $.ajax({
        type: 'post',
        url: '{$chatHoldUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},  
    })
    .done(function(dataResponse) {
        modal.find('.modal-title').html('Client Chat Hold');
        modal.find('.modal-body').html(dataResponse);        
    })
    .fail(function(xhr, textStatus, errorThrown) {
        if (xhr.status === 403) {
            createNotify('Error', xhr.responseText, 'error');
        } else {
            createNotify('Error', 'Internal Server Error. Try again letter.', 'error');
        }  
        setTimeout(function () {
            modal.modal('hide');
        }, 900) 
    })
    .always(function(jqXHR, textStatus, errorThrown) {  
        setTimeout(function () {
            btnHold.html(btnContent).removeClass('btn-default').prop('disabled', false);  
        }, 1000);       
    });     
});

$(document).on('click', '.cc_un_hold', function (e) {
    e.preventDefault();
    
    if(!confirm('Are you sure want to make UnHold?')) {
        return false;
    } 
    
    let cchId = $(this).attr('data-cch-id');
    let btnProgress = $(this);
    let btnContent = btnProgress.html();
        
    btnProgress.html('<i class="fa fa-cog fa-spin"></i> Loading...')
        .addClass('btn-default')
        .prop('disabled', true);
    
    $.ajax({
        url: '{$chatUnHoldUrl}',
        type: 'POST',
        data: {cchId: cchId},
        dataType: 'json'    
    })
    .done(function(dataResponse) {
        if (dataResponse.status === 1) { 
            createNotify('Success', dataResponse.message, 'success'); 
            refreshChatPage(cchId);                        
        } else if (dataResponse.message.length) {
            createNotify('Error', dataResponse.message, 'error');
        } else {
            createNotify('Error', 'Error, please check logs', 'error');
        }
        btnProgress.html(btnContent).removeClass('btn-default').prop('disabled', false);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        createNotify('Error', jqXHR.responseText, 'error');
        btnProgress.html(btnContent).removeClass('btn-default').prop('disabled', false);      
    })
    .always(function(jqXHR, textStatus, errorThrown) {  
        setTimeout(function () {
            btnProgress.html(btnContent).removeClass('btn-default').prop('disabled', false);  
        }, 3000);
    });           
});

$(document).on('click', '.cc_take', function (e) {
    e.preventDefault();
    
    if(!confirm('Are you sure want to Take the chat?')) {
        return false;
    } 
    
    let cchId = $(this).attr('data-cch-id');
    let btnSubmit = $(this);
    let btnContent = btnSubmit.html();
        
    btnSubmit.html('<i class="fa fa-cog fa-spin"></i> Loading...')
        .addClass('btn-default')
        .prop('disabled', true);
        
    $('#page-loader').show();    
    
    $.ajax({
        url: '{$clientChatTakeUrl}',
        type: 'POST',
        data: {cchId: cchId},
        dataType: 'json'    
    })
    .done(function(dataResponse) {
        $('#page-loader').hide();
        if (dataResponse.status > 0) { 
            createNotify('Success', dataResponse.message, 'success');
            $(location).attr('href', '/client-chat/index?chid=' + dataResponse.goToClientChatId);                     
        } else if (dataResponse.message.length) {
            createNotify('Error', dataResponse.message, 'error');
        } else {
            createNotify('Error', 'Error, please check logs', 'error');
        }
        btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        $('#page-loader').hide();
        createNotify('Error', jqXHR.responseText, 'error');
        btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
    })
    .always(function(jqXHR, textStatus, errorThrown) {  
        setTimeout(function () {
            $('#page-loader').hide();
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        }, 3000);
    });           
});

$(document).on('click', '.cc_return', function (e) {
    e.preventDefault();
    
    if(!confirm('Are you sure want to Return the chat to In Progress?')) {
        return false;
    } 
    
    let cchId = $(this).attr('data-cch-id');
    let btnSubmit = $(this);
    let btnContent = btnSubmit.html();
        
    btnSubmit.html('<i class="fa fa-cog fa-spin"></i> Loading...')
        .addClass('btn-default')
        .prop('disabled', true);
        
    $('#page-loader').show();    
    
    $.ajax({
        url: '{$clientChatReturnUrl}',
        type: 'POST',
        data: {cchId: cchId},
        dataType: 'json'    
    })
    .done(function(dataResponse) {
        $('#page-loader').hide();
        if (dataResponse.status > 0) { 
            createNotify('Success', dataResponse.message, 'success');
            $(location).attr('href', '/client-chat/index?chid=' + dataResponse.goToClientChatId);                     
        } else if (dataResponse.message.length) {
            createNotify('Error', dataResponse.message, 'error');
        } else {
            createNotify('Error', 'Error, please check logs', 'error');
        }
        btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        $('#page-loader').hide();
        createNotify('Error', jqXHR.responseText, 'error');
        btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
    })
    .always(function(jqXHR, textStatus, errorThrown) {  
        setTimeout(function () {
            $('#page-loader').hide();
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        }, 3000);
    });           
});
    
$(document).on('click', '#reset_additional', function (e) {
    e.stopPropagation();
    e.preventDefault();      
    $('#additional_filters_div').find('input,select').each(function() {
        $(this).val('');          
    }); 
    $('#resetAdditionalFilter').val('1');
    window.updateClientChatFilter("{$filter->getId()}", "{$filter->formName()}", "{$loadChannelsUrl}");
});  

$(document).on('click', '#btn_additional_filters', function (e) {
    e.stopPropagation();
    e.preventDefault();  
    $('#additional_filters_div').toggle();  
});


$(document).on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if ($(this).hasClass('checked')) {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.selectedChats = '{}';
            //sessionStorage.removeItem('selectedUsers');
        } else {
            btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');
            let params = new URLSearchParams(window.location.search);
            console.log(params.toString());
            $.ajax({
             type: 'post',
             dataType: 'json',
             //data: {},
             url: '$selectAllUrl' + '?' + params.toString() + '&act=select-all',
             success: function (data) {
                let cnt = Object.keys(data).length
                if (data) {
                    let jsonData = JSON.stringify(data);
                    sessionStorage.selectedChats = jsonData;
                    btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
                   
                    $('.select-on-check-all').prop('checked', true); //.trigger('click');
                    $("input[name='selection[]']").prop('checked', true);
                } else {
                    btn.html('<span class="fa fa-square-o"></span> Check All');
                }
             },
             error: function (error) {
                    btn.html('<span class="fa fa-error text-danger"></span> Error ...');
                    console.error(error);
                    alert('Request Error');
                 }
             });
        }
});

function refreshUserSelectedState() {
     if (sessionStorage.selectedChats) {
        let data = jQuery.parseJSON( sessionStorage.selectedChats );
        let btn = $('#btn-check-all');
        
        let cnt = Object.keys(data).length;
        if (cnt > 0) {
            $.each( data, function( key, value ) {
              $("input[name='selection[]'][value=" + value + "]").prop('checked', true);
            });
            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
            
        } else {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
        }
    }
}

$(document).on('click', '.multiple-checkbox', function(e) {
    e.stopPropagation();
    let checked = $("input[name='selection[]']:checked").map(function () { return this.value; }).get();
    let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
    let data = [];
    if (sessionStorage.selectedChats) {
        data = jQuery.parseJSON( sessionStorage.selectedChats );
    }
   
    $.each( checked, function( key, value ) {
        if (typeof data[value] === 'undefined') {
          data[value] = value;
        }
    });
    
   $.each( unchecked, function( key, value ) {
      if (typeof data[value] !== 'undefined') {
            delete(data[value]);
      }
    });
   
   sessionStorage.selectedChats = JSON.stringify(data);
   refreshUserSelectedState();
});

$(document).on('click', '.btn-multiple-update', function(e) {
e.preventDefault();        
let arrIds = [];
if (sessionStorage.selectedChats) {
    let data = jQuery.parseJSON( sessionStorage.selectedChats );
    arrIds = Object.values(data);
    
    console.log(arrIds);
    // $('#user_list_json').val(JSON.stringify(arrIds));
    
    let modal = $('#modal-sm');
    
    $.ajax({
        type: 'post',
        url: '{$clientChatMultipleUpdate}',
        dataType: 'html',
        cache: false,
        data: {chatIds: arrIds.length ? JSON.stringify(arrIds) : ''},
        beforeSend: function () {
            modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            modal.find('.modal-title').html('Client Chat Multiple Update');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {                  
            modal.find('.modal-body').html('Error: ' + xhr.responseText);
            //createNotify('Error', xhr.responseText, 'error');
        },
    });
}
});
 
$(document).on('click', '.js-couch-note-btn', function (e) {
    e.stopPropagation();
    e.preventDefault(); 
    
    let btnSubmit = $(this);
    let btnContent = btnSubmit.html();
        
    btnSubmit.html('<i class="fa fa-cog fa-spin"></i>...')
        .addClass('btn-default')
        .prop('disabled', true);
         
    $.ajax({
        url: '{$clientChatCouchNoteUrl}',
        type: 'POST',
        data: $('#ClientChatCouchNoteForm').serialize(),
        dataType: 'json'    
    })
    .done(function(dataResponse) {        
        if (dataResponse.status > 0) { 
            createNotify('Success', dataResponse.message, 'success');
            $('#couchNoteMessage').val('');
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

refreshUserSelectedState();
JS;
$this->registerJs($js);

$css = <<<CSS
    .panel_toolbox .btn {
        border-radius: 100%;
        width: 25px;
        height: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #pjax-notes .x_panel {
        margin-top: 10px;
    }      
CSS;
$this->registerCss($css);
