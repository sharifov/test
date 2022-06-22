function socketSend(controller, action, params)
{
    let data = {};
    data.c = controller;
    data.a = action;
    data.p = params;
    //console.log(data);
    socket.send(JSON.stringify(data));
}

/**
 * Send a message to the WebSocket server
 */
function onSendClick()
{
    if (socket.readyState != socket.OPEN) {
        console.error("Socket is not open: " + socket.readyState);
        return;
    }
    var msg = document.getElementById("message").value;
    socket.send(msg);
}

function pushDialogOnTop(chatID)
{
    let parentElement = document.getElementById('cc-dialogs-wrapper')
    let childElement = document.getElementById('dialog-' + chatID)
    let topChatId = parentElement.firstElementChild.id

    if (chatID != topChatId.split("-")[1]) {
        let obj = $("#dialog-" + chatID);
        obj.hide('25000', function () {
            parentElement.insertBefore(childElement, parentElement.firstChild)
        });

        obj.show('25000');
    }
}

window.sendCommandUpdatePhoneWidgetCurrentCalls = function (finishedCallSid, userId, generalLinePriorityIsEnabled) {
    socketSend('Call', 'GetCurrentQueueCalls', {
        'userId': userId,
        'finishedCallSid': finishedCallSid,
        'generalLinePriorityIsEnabled': generalLinePriorityIsEnabled
    });
};

function Buid (userId) {
    this.key = 'buid' + userId;

    this.get = function () {
        return localStorage.getItem(this.key) || null;
    }

    this.set = function (buid) {
        localStorage.setItem(this.key, buid);
    }

    this.reset = function () {
        localStorage.removeItem(this.key);
    }
}

function wsInitConnect(wsUrl, reconnectInterval, userId, onlineObj, ccNotificationUpdateUrl, discardUnreadMessageUrl)
{
    try {
        let buid = new Buid(userId);
        //const socket = new WebSocket(wsUrl);
        var socket = new ReconnectingWebSocket(wsUrl, null, {debug: false, reconnectInterval: reconnectInterval});
        window.socket = socket;

        socket.onopen = function (e) {
            //socket.send('{"user2_id":' + user_id + '}');
            console.info('Socket Status: ' + socket.readyState + ' (Open)');
            onlineObj.attr('title', 'Online Connection: opened').find('i').removeClass('danger').addClass('warning');
            // console.log(e);

            if (typeof PhoneWidget === 'object') {
                socketSend('PhoneDeviceRegister', 'Register', {buid: buid.get()});
            }
        };

        socket.onmessage = function (e) {
            // onlineObj.find('i').removeClass('danger').removeClass('success').addClass('warning');
            console.info('socket.onmessage');
            try {
                var obj = JSON.parse(e.data); // $.parseJSON( e.data );
                console.log(obj);
            } catch (error) {
                console.error('Invalid JSON data on socket.onmessage');
                console.error(e.data);
            }

            try {
                if (typeof obj.cmd !== 'undefined') {
                    if (obj.cmd === 'initConnection') {
                        if (typeof obj.uc_id !== 'undefined') {
                            if (obj.uc_id > 0) {
                                window.socketConnectionId = obj.uc_id;
                                if (typeof addChatToActiveConnection ===  "function") {
                                    addChatToActiveConnection();
                                }
                                onlineObj.attr('title', 'Online Connection (' + obj.uc_id + '): true').find('i').removeClass('warning').removeClass('danger').addClass('success');
                            } else {
                                onlineObj.attr('title', 'Timeout DB connection: restart service').find('i').removeClass('danger').removeClass('success').addClass('warning');
                            }
                        }
                    }

                    if (obj.cmd === 'userNotInit') {
                        window.location.href = '/site/logout';
                    }


                    if (obj.cmd === 'getNewNotification') {
                        //alert(obj.cmd);
                        if (typeof obj.notification !== 'undefined') {
                            if (userId == obj.notification.userId) {
                                if (typeof notificationInit === 'undefined') {
                                    console.warn('not found notificationInit method');
                                } else {
                                    notificationInit(obj.notification);
                                }
                            } else {
                                console.error('connecting user Id not equal notification user Id');
                            }
                        } else {
                            updatePjaxNotify();
                        }
                    }

                    if (obj.cmd === 'updateCommunication' && typeof updateCommunication === 'function') {
                        // updatePjaxNotify();
                        updateCommunication();
                    }

                    if (obj.cmd === 'callUpdate') {
                        if (typeof PhoneWidget === 'object') {
                            PhoneWidget.refreshCallStatus(obj);
                        }

                        if (typeof webCallLeadRedialUpdate === "function") {
                            webCallLeadRedialUpdate(obj);
                        }

                        if (obj.status === 'In progress') {
                            $("#incomingCallAudio").prop('muted', true);
                        }
                    }

                    if (obj.cmd === 'webCallUpdate') {
                        //console.info('webCallUpdate - 1');
                        if (typeof webCallUpdate === "function") {
                            //console.info('webCallUpdate - 2');
                            webCallUpdate(obj);
                        }
                    }

                    if (obj.cmd === 'recordingUpdate') {
                        updatePjaxNotify();
                        updateCommunication();
                    }

                    if (obj.cmd === 'updateUserCallStatus') {
                        if (typeof PhoneWidget === 'object') {
                            PhoneWidget.changeStatus(obj.type_id);
                        }

                        if (typeof PhoneWidget === 'object') {
                            PhoneWidget.refreshCallStatus(obj);
                        }
                    }

                    if (obj.cmd === 'updateIncomingCall') {
                        if (typeof PhoneWidget === 'object') {
                            if (typeof obj.status !== 'undefined') {
                                PhoneWidget.requestIncomingCall(obj);
                            }
                        }
                    }

                    if (obj.cmd === 'addPriorityCall') {
                        if (typeof PhoneWidget === 'object') {
                            if (typeof obj.data !== 'undefined') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'removePriorityCall') {
                        if (typeof PhoneWidget === 'object') {
                            if (typeof obj.data !== 'undefined') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'resetPriorityCall') {
                        if (typeof PhoneWidget === 'object') {
                            if (typeof obj.data !== 'undefined') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'clientChatRequest') {
                        if (typeof refreshClientChatWidget === "function") {
                            refreshClientChatWidget(obj);
                        }
                    }

                    if (obj.cmd === 'refreshDialogToken') {
                        if (typeof refreshDialogToken === "function") {
                            refreshDialogToken(obj);
                        }
                    }


                    if (obj.cmd === 'callMapUpdate') {
                        $('#btn-user-call-map-refresh').click();
                    }

                    if (obj.cmd === 'openUrl') {
                        window.open(obj.url); //, '_blank'
                        /*var hiddenLink = $("#hidden_link");
                        hiddenLink.attr("href", obj.url);
                        hiddenLink.attr("target", "_blank");
                        hiddenLink.attr("data-pjax", "0");
                        hiddenLink[0].click();*/
                    }

                    if (obj.cmd === 'phoneWidgetSmsSocketMessage') {
                        if (typeof obj.data !== 'undefined') {
                            PhoneWidgetSms.socket(obj.data);
                        }
                    }

                    if (obj.cmd === 'holdCall') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'muteCall') {
                        if (typeof obj.data !== 'undefined') {
                            PhoneWidget.socket(obj.data);
                        }
                    }

                    if (obj.cmd === 'missedCall') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'hidePhoneNotifications') {
                        if (typeof PhoneWidget === 'object') {
                            PhoneWidget.hidePhoneNotifications();
                        }
                    }

                    if (obj.cmd === 'removeIncomingRequest') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.removeIncomingRequest(obj.data.call.sid);
                            }
                        }
                    }

                    if (obj.cmd === 'completeCall') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.completeCall(obj.data.call.sid);
                            }
                        }
                    }

                    if (obj.cmd === 'callAlreadyTaken') {
                        createNotify('Accept Call', 'The call has already been taken by another agent', 'warning');
                        if (typeof PhoneWidget === 'object') {
                            PhoneWidget.removeIncomingRequest(obj.callSid);
                        }
                    }

                    if (obj.cmd === 'conferenceUpdate') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'clientChatUnreadMessage') {
                        let activeChatId = localStorage.getItem('activeChatId');

                        if (document.visibilityState == "visible" && window.name === 'chat' && activeChatId == obj.data.cchId && obj.data.cchUnreadMessages) {
                            $.post(discardUnreadMessageUrl, {cchId: activeChatId});
                            return false;
                        }

                        let previousPage = localStorage.getItem('previousPage');
                        if ((document.visibilityState == "visible") && obj.data.soundNotification && window.name === 'chat') {
                            soundNotification('incoming_message');
                        } else if (previousPage === $(document)[0].baseURI && obj.data.soundNotification) {
                            soundNotification('incoming_message');
                        }

                        if (obj.data.totalUnreadMessages) {
                            $('._cc_unread_messages').html(obj.data.totalUnreadMessages);
                            if (window.name === 'chat') {
                                faviconChat.badge(obj.data.totalUnreadMessages);
                            }
                        } else {
                            $('._cc_unread_messages').html('');
                            faviconChat.reset();
                            if (obj.data.refreshPage) {
                                window.location.reload();
                                return false;
                            }
                        }

                        if (obj.data.cchId && (obj.data.cchUnreadMessages === null || obj.data.cchUnreadMessages > 0)) {
                            $("._cc-chat-unread-message").find("[data-cch-id='" + obj.data.cchId + "']").html(obj.data.cchUnreadMessages);
                        }
                        // if (obj.data.cchId) {
                        // if($('#chat-last-message-refresh-' + obj.data.cchId).length > 0){
                        //pjaxReload({container: '#chat-last-message-refresh-' + obj.data.cchId, async: false});
                        //pushDialogOnTop(obj.data.cchId)
                        // }
                        // if($('#pjax-chat-additional-data-' + obj.data.cchId).length > 0){
                        //  pjaxReload({container: '#pjax-chat-additional-data-' + obj.data.cchId, async: false});
                        // }
                        // }

                        if (obj.data.shortMessage) {
                            let lastMessageValue = $('#chat-last-message-' + obj.data.cchId);
                            if (lastMessageValue.length > 0) {
                                lastMessageValue.html('<p title="Last ' + obj.data.messageOwner + ' message"><small>' + obj.data.shortMessage + '</small></p>');
                                pushDialogOnTop(obj.data.cchId)
                            }
                        }
                        if ($('#notify-pjax-cc').length > 0) {
                            pjaxReload({container: '#notify-pjax-cc', url: ccNotificationUpdateUrl});
                        }

                        if (obj.data.cchId && obj.data.moment) {
                            let seconds = + obj.data.moment;
                            $("._cc-item-last-message-time[data-cch-id='" + obj.data.cchId + "']").attr('data-moment', obj.data.moment).html(moment.duration(-seconds, 'seconds').humanize(true));
                        }
                    }

                    if (obj.cmd === 'clientChatUpdateItemInfo') {
                        let seconds = + obj.data.moment;
                        $("._cc-item-last-message-time[data-cch-id='" + obj.data.cchId + "']").attr('data-moment', obj.data.moment).html(moment.duration(-seconds, 'seconds').humanize(true));
                        let lastMessageValue = $('#chat-last-message-' + obj.data.cchId);
                        if (lastMessageValue.length > 0) {
                            lastMessageValue.html('<p title="Last ' + obj.data.messageOwner + ' message"><small>' + obj.data.shortMessage + '</small></p>');
                            pushDialogOnTop(obj.data.cchId)
                        }
                    }

                    if (obj.cmd === 'clientChatUpdateClientStatus') {
                        if (obj.cchId) {
                            $('._cc-list-wrapper').find('[data-cch-id="' + obj.cchId + '"]').find('._cc-status').attr('data-is-online', obj.isOnline);
                            $('.client-chat-client-info-wrapper').find('._cc-status').attr('data-is-online', obj.isOnline);
                        }
                        //createNotify('Client Chat Notification', obj.statusMessage, obj.isOnline ? 'success' : 'warning');
                    }

                    // if (obj.cmd === 'clientChatUpdateTimeLastMessage') {
                    //     if (obj.data.cchId) {
                    //         $("._cc-item-last-message-time[data-cch-id='"+obj.data.cchId+"']").attr('data-moment', obj.data.moment).html(obj.data.dateTime);
                    //     }
                    // }

                    if (obj.cmd === 'refreshChatPage') {
                        let activeChatId = localStorage.getItem('activeChatId');
                        if (typeof window.refreshChatPage === 'function' && window.name === 'chat' && activeChatId == obj.data.cchId) {
                            $("#modal-sm").modal("hide");
                            window.refreshChatPage(obj.data.cchId);
                            createNotify('Warning', obj.data.message, 'warning');
                        }
                    }

                    if (obj.cmd === 'logout') {
                        if (typeof window.autoLogout === "function") {
                            window.autoLogout(obj.timerSec, obj.isShowMessage);
                        }
                    }

                    if (obj.cmd === 'forceLogout') {
                        window.location.href = '/site/logout?type=autologout';
                    }

                    if (obj.cmd === 'forceRefresh') {
                        window.location.reload();
                    }

                    if (obj.cmd === 'PhoneDeviceRegister') {
                        if (obj.error) {
                            if (obj.buidIsInvalid) {
                                buid.reset();
                                alert(obj.msg);
                            }
                            if (obj.deviceIsInvalid) {
                                alert(obj.msg);
                            }
                            createNotify('Phone Widget', obj.msg, 'error');
                            PhoneWidget.addLog(obj.msg);
                        } else {
                            if (obj.buid) {
                                buid.set(obj.buid);
                            }
                            window.phoneWidget.device.initialize.Init(obj.deviceId, obj.devices, window.phoneDeviceRemoteLogsEnabled);
                            window.sendCommandUpdatePhoneWidgetCurrentCalls('', userId, window.generalLinePriorityIsEnabled);
                        }
                    }

                    if (obj.cmd === 'PhoneDeviceReady') {
                        if (obj.error) {
                            if (obj.deviceIsInvalid) {
                                PhoneWidget.getDeviceState().removeDeviceId();
                            }
                            PhoneWidget.addLog(obj.msg);
                            createNotify('Phone Widget', obj.msg.message, 'error');
                        }
                    }

                    if (obj.cmd === 'updateCurrentCalls') {
                        if (typeof PhoneWidget === "object") {
                            PhoneWidget.updateCurrentCalls(obj.data, obj.userStatus);
                        }
                    }

                    if (obj.cmd === 'addCallToHistory') {
                        if (window.tabHistoryLoaded) {
                            if (typeof PhoneWidget === "object") {
                                PhoneWidget.socket(obj.data);
                            }
                        } else {
                            console.log('History not loaded.');
                        }
                    }

                    if (obj.cmd === 'showNotification') {
                        let data = obj.data;
                        createNotify(data.title, data.message, data.type);
                    }

                    if (obj.cmd === 'showDesktopNotification') {
                        let data = obj.data;
                        createDesktopNotify(data.desktopId, data.title, data.message, data.type, data.desktopMessage);
                        if (data.showBrowserNotify) {
                            createNotify(data.title, data.message, data.type);
                        }
                    }

                    if (obj.cmd === 'updateVoiceMailRecord') {
                        if ($("#voice-mail-pjax").length > 0) {
                            pjaxReload({container: "#voice-mail-pjax"});
                        }
                        window.updateVoiceRecordCounters();
                    }

                    if (obj.cmd === 'reloadClientChatList') {
                        if (typeof window.refreshChannelList === 'function') {
                            window.refreshChannelList();
                        }
                    }

                    if (obj.cmd === 'reloadChatInfo') {
                        let boxElement = $('#_cc_additional_info_wrapper');
                        if (boxElement.length) {
                            if (!('data' in obj)) {
                                console.error('Error: reloadChatInfo - "data" required in "obj"');
                                return;
                            }
                            if (!('cchId' in obj.data)) {
                                console.error('Error: reloadChatInfo - "cchId" in "obj.data"');
                                return;
                            }

                            let activeChatId = parseInt(localStorage.getItem('activeChatId'), 10);
                            let cchId = parseInt(obj.data.cchId, 10);

                            if (activeChatId === cchId) {
                                window.refreshChatInfo(cchId);
                                if (obj.data.message) {
                                    createNotify('Warning', obj.data.message, 'warning');
                                }
                            }
                        }
                    }

                    if (obj.cmd === 'clientChatAddQuoteButton') {
                        let chatId = parseInt(obj.data.chatId, 10);
                        let leadId = parseInt(obj.data.leadId, 10);
                        let content = '<a class="chat-offer dropdown-item" data-chat-id="' + chatId + '" data-lead-id="' + leadId + '" data-url="'+obj.data.url+'"><i class="fa fa-plane"> </i> Send Quotes</a>';
                        $(document).find('span[data-cc-lead-info-quote="' + leadId + '"]').html(content);
                    }

                    if (obj.cmd === 'clientChatRemoveQuoteButton') {
                        let chatId = parseInt(obj.data.chatId, 10);
                        let leadId = parseInt(obj.data.leadId, 10);
                        $(document).find('span[data-cc-lead-info-quote="' + leadId + '"]').html("");
                    }

                    if (obj.cmd === 'clientChatAddOfferButton') {
                        let chatId = parseInt(obj.data.chatId, 10);
                        let leadId = parseInt(obj.data.leadId, 10);
                        let content = '<a class="chat-offer dropdown-item" data-chat-id="' + chatId + '" data-lead-id="' + leadId + '" data-url="'+obj.data.url+'"><i class="fa fa-plane"> </i> Offer</a>';
                        $(document).find('span[data-cc-lead-info-offer="' + leadId + '"]').html(content);
                    }

                    if (obj.cmd === 'clientChatRemoveOfferButton') {
                        let chatId = parseInt(obj.data.chatId, 10);
                        let leadId = parseInt(obj.data.leadId, 10);
                        $(document).find('span[data-cc-lead-info-offer="' + leadId + '"]').html("");
                    }

                    if (obj.cmd === 'recordingEnable') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'recordingDisable') {
                        if (typeof obj.data !== 'undefined') {
                            if (typeof PhoneWidget === 'object') {
                                PhoneWidget.socket(obj.data);
                            }
                        }
                    }

                    if (obj.cmd === 'addFileToFileStorageList') {
                        if (typeof addFileToFileStorageList === "function") {
                            addFileToFileStorageList(obj);
                        }
                    }

                    if (obj.cmd === 'addedQuote') {
                        let counter = $('.product-quote-counter-' + obj.data.productId);
                        if (counter) {
                            let count = parseInt(counter.data('value'));
                            counter.data('value', (count + 1));
                            counter.html('<sup title="Number of quotes">(' + (count + 1) + ')</sup>');
                        }
                    }

                    if (obj.cmd === 'removedQuote') {
                        let counter = $('.product-quote-counter-' + obj.data.productId);
                        if (counter) {
                            let count = parseInt(counter.data('value'));
                            if (count > 0) {
                                count--;
                                if (count > 0) {
                                    counter.data('value', count);
                                    counter.html('<sup title="Number of quotes">(' + count + ')</sup>');
                                } else {
                                    counter.data('value', count);
                                    counter.html('');
                                }
                            }
                        }
                    }

                    if (obj.cmd === 'reloadOrders') {
                        setTimeout(function(){ pjaxReload({container: '#pjax-lead-orders', async: false, timeout: 5000}); }, 2000);
                    }

                    if (obj.cmd === 'reloadOffers') {
                        setTimeout(function(){ pjaxReload({container: '#pjax-lead-offers', async: false, timeout: 5000}); }, 2000);
                    }

                    if (obj.cmd === 'reloadFlightDefaultQuotes') {
                        setTimeout(function(){ pjaxReload({container: '#quotes_list', async: false, timeout: 5000}); }, 2000);
                    }

                    if (obj.cmd === 'quoteBooked') {
                        $('.btn-delete-product[data-product-id="' + obj.data.productId + '"]').hide();
                    }

                    if (obj.cmd === 'leadRedialAutoTake') {
                        if (typeof PhoneWidget === 'object' && obj.data.callSid === window.phoneWidget.device.activeCallSid) {
                            //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
                            let windowObjectReference = window.open(PhoneWidget.getLeadViewPageShortUrl() + '/' + obj.data.leadGid, 'window' + obj.data.leadId); //, strWindowFeatures);
                            windowObjectReference.focus();
                        }
                    }
                    if (obj.cmd === 'reloadShitScheduleRequest') {
                        $(document).trigger('reloadShitScheduleRequest')
                    }
                }
                // onlineObj.find('i').removeClass('danger').removeClass('warning').addClass('success');
            } catch (error) {
                console.error('Error in functions - socket.onmessage');
                console.error(error);
            }

        };

        socket.onclose = function (event) {

            if (event.wasClean) {
                console.log('Connection closed success (Close)');
            } else {
                console.error('Disconnect (Error)'); // Example kill process of server
            }
            //console.log('Code: ' + event.code);

            onlineObj.attr('title', 'Disconnect').find('i').removeClass('success').addClass('danger');
            window.socketConnectionId = null;

            if (typeof PhoneWidget === 'object' && PhoneWidget.isInitiated()) {
                if (typeof twilioLogger === 'object') {
                    twilioLogger.error('%s', 'WS connection close');
                }
                PhoneWidget.getDeviceState().phoneDisconnected('WS connection closed');
                PhoneWidget.getDeviceState().resetDevices('WS connection closed');
            }

            // setTimeout(function() {
            //   wsInitConnect();
            // }, 5000);
            //console.log('Socket Status: ' + socket.readyState + ' (Closed)');
        };

        socket.onerror = function (event) {
            //if (socket.readyState == 1) {
            console.log('Socket error: ' + event.message);
            //}
            onlineObj.attr('title', 'Online Connection: false').find('i').removeClass('success').addClass('danger');
            window.socketConnectionId = null;

            if (typeof PhoneWidget === 'object' && PhoneWidget.isInitiated()) {
                if (typeof twilioLogger === 'object') {
                    twilioLogger.error('%s', 'WS connection error');
                }
                PhoneWidget.getDeviceState().phoneDisconnected('WS connection error');
                PhoneWidget.getDeviceState().resetDevices('WS connection error');
            }
        };
    } catch (error) {
        console.error(error);
        onlineObj.attr('title', 'Online Connection: error').find('i').removeClass('success').addClass('danger');
        window.socketConnectionId = null;
    }
}