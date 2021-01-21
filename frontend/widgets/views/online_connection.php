<?php

/* @var $leadId integer */

use common\models\UserConnection;
use sales\model\user\entity\monitor\UserMonitor;
use yii\bootstrap4\Modal;
use yii\helpers\Url;

/* @var $caseId integer */
/* @var $userId integer */
/* @var $userIdentity integer */
/* @var $controllerId string */
/* @var $actionId string */
/* @var $pageUrl string */
/* @var $ipAddress string */
/* @var $webSocketHost string */
/* @var $subList array */
/* @var $this \yii\web\View */

\frontend\assets\WebSocketAsset::register($this);
if (UserConnection::isIdleMonitorEnabled()) {
    \frontend\assets\IdleAsset::register($this);
}

$bundle = \frontend\assets\TimerAsset::register($this);
if (UserMonitor::isAutologoutEnabled()) {
    \frontend\assets\BroadcastChannelAsset::register($this);
}

$dtNow = date('Y-m-d H:i:s');

?>
    <li>
        <a href="javascript:;" class="info-number" title="Online Connection" id="online-connection-indicator">
            <i class="fa fa-plug"></i>
            <?php /*//php if($newCount): ?>
                <span class="badge" title="Open tabs"></span>
            <?php //php endif;*/?>
        </a>
        <script>
            function socketSend(controller, action, params) {
                let data = {};
                data.c = controller;
                data.a = action;
                data.p = params;
                //console.log(data);
                socket.send(JSON.stringify(data));
            }
        </script>
    </li>
    <?php if (UserConnection::isIdleMonitorEnabled()) : ?>
    <li>
        <a href="javascript:;" class="info-number" title="User Monitor" id="user-monitor-indicator">
            <div class="text-success"><i class="fa fa-clock-o"></i> <span id="user-monitor-timer"></span></div>
        </a>
    </li>
        <?php
        //$this->registerJs("$('#user-monitor-timer').timer({format: '%M:%S', seconds: 0}).timer('start');", \yii\web\View::POS_READY, 'user-monitor-timer');
        ?>
    <?php endif; ?>


<?php if (UserMonitor::isAutologoutEnabled()) : ?>
    <?php Modal::begin([
        'id' => 'modal-autologout',
        'closeButton' => false,
        'title' => '<i class="fa fa-power-off"></i> Auto LogOut',
        'size' => Modal::SIZE_SMALL,
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
    ])?>
        <div class="text-center">
            <p>You are not active for a long time (<?=UserMonitor::autologoutIdlePeriodMin()?> min.). After a few seconds, the system will automatically log out.</p>
            <?php if (UserMonitor::isAutologoutTimerSec()) : ?>
                <h1 id="autologout-timer" class="text-danger">00:00</h1>
            <?php endif; ?>
            <p><b>Do you want to continue working?</b></p>
            <button class="btn btn-danger" id="btn-logout"><i class="fa fa-power-off"></i> LogOut</button>
            <button class="btn btn-success" id="btn-cancel-autologout"><i class="fa fa-check"></i> Continue working</button>
        </div>
    <?php Modal::end()?>
<?php endif; ?>


<?php
// $userAgent = urlencode(Yii::$app->request->userAgent);
// const socket = new WebSocket('wss:\\sales.dev.travelinsides.com:8888/?user_id=1&controller_id=test&action_id=test&page_url=test&lead_id=15636');

$urlParams = [
    'user_id' => $userId,
    'controller_id' => $controllerId,
    'action_id' => $actionId,
    'page_url' => $pageUrl,
    'lead_id' => $leadId,
    'case_id' => $caseId,
    'ip'    => $ipAddress,
    'sub_list' => $subList,
];

if ($leadId) {
    $urlParams['lead_id'] = $leadId;
}

if ($caseId) {
    $urlParams['case_id'] = $caseId;
}
$urlParamsStr = http_build_query($urlParams);

$wsUrl = $webSocketHost . '/?' . $urlParamsStr;
$ccNotificationUpdateUrl = Url::to(['/client-chat/refresh-notification']);
$discardUnreadMessageUrl = Url::to(['/client-chat/discard-unread-messages']);
$js = <<<JS
   
    window.socket = null;
    window.socketConnectionId = null;

    /**
     * Send a message to the WebSocket server
     */
    function onSendClick() {
        if (socket.readyState != socket.OPEN) {
            console.error("Socket is not open: " + socket.readyState);
            return;
        }
        var msg = document.getElementById("message").value;
        socket.send(msg);
    }
    
    function pushDialogOnTop(chatID) {           
        let parentElement = document.getElementById('cc-dialogs-wrapper')
        let childElement = document.getElementById('dialog-' + chatID)
        let topChatId = parentElement.firstElementChild.id
            
        if (chatID != topChatId.split("-")[1]){       
            $("#dialog-" + chatID).hide('25000', function() {
                parentElement.insertBefore(childElement, parentElement.firstChild)
            }); 
                      
            $("#dialog-" + chatID).show('25000');       
        }                
    }
    
    // function sortDialogOnLoad() {        
    //     $('._cc_item_unread_messages').each(function(i, obj) { 
    //         if ($(this).text()){
    //             pushDialogOnTop($(this).data('cch-id'))                
    //         }                           
    //     });   
    // }
    // sortDialogOnLoad() 
    

    const userId = '$userId';
    window.userIdentity = '$userIdentity';
    const wsUrl = '$wsUrl';
    const onlineObj = $('#online-connection-indicator');
    
    window.sendCommandUpdatePhoneWidgetCurrentCalls = function (finishedCallSid) {
         socketSend('Call', 'GetCurrentQueueCalls', {'userId': userId, 'finishedCallSid': finishedCallSid});
    };
    
    function wsInitConnect(){
        
        try {
    
            //const socket = new WebSocket(wsUrl);
            var socket = new ReconnectingWebSocket(wsUrl, null, {debug: false, reconnectInterval: 10000});
            window.socket = socket;
            
            socket.onopen = function (e) {
                //socket.send('{"user2_id":' + user_id + '}');
                console.info('Socket Status: ' + socket.readyState + ' (Open)');
                onlineObj.attr('title', 'Online Connection: opened').find('i').removeClass('danger').addClass('warning');
                // console.log(e);
                
                window.sendCommandUpdatePhoneWidgetCurrentCalls('');
               
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
                        
                        if(obj.cmd === 'initConnection') {
                            if (typeof obj.uc_id !== 'undefined') {
                                if(obj.uc_id > 0) {
                                    window.socketConnectionId = obj.uc_id;
                                    if (typeof addChatToActiveConnection ===  "function") {
                                        addChatToActiveConnection();
                                    }
                                    onlineObj.attr('title', 'Online Connection (' + obj.uc_id +'): true').find('i').removeClass('warning').removeClass('danger').addClass('success');
                                } else {
                                    onlineObj.attr('title', 'Timeout DB connection: restart service').find('i').removeClass('danger').removeClass('success').addClass('warning');
                                }    
                            }
                        }
                        
                        if(obj.cmd === 'userNotInit') {
                            window.location.href = '/site/logout';
                        }
                        
                        
                        if(obj.cmd === 'getNewNotification') {
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
                                            
                        if(obj.cmd === 'updateCommunication' && typeof updateCommunication === 'function') {
                            // updatePjaxNotify();
                            updateCommunication();
                        }
                        
                        if(obj.cmd === 'callUpdate') {
                            
                             if (typeof PhoneWidgetCall === 'object') {
                                PhoneWidgetCall.refreshCallStatus(obj);
                             }
                            
                             if (typeof webCallLeadRedialUpdate === "function") {
                                webCallLeadRedialUpdate(obj);
                            }
                             
                             if (obj.status === 'In progress') {
                                 $("#incomingCallAudio").prop('muted', true);
                             }
                        }
                        
                        if(obj.cmd === 'webCallUpdate') {
                            //console.info('webCallUpdate - 1');
                            if (typeof webCallUpdate === "function") {
                                //console.info('webCallUpdate - 2');
                                webCallUpdate(obj);
                            }
                            
                            
                        }
                        
                        if(obj.cmd === 'recordingUpdate') {
                            updatePjaxNotify();
                            updateCommunication();
                        }
                        
                        if(obj.cmd === 'updateUserCallStatus') {
                            
                             if (typeof PhoneWidgetCall === 'object') {
                                 PhoneWidgetCall.changeStatus(obj.type_id);
                            }
                             
                            if (typeof PhoneWidgetCall === 'object') {
                                PhoneWidgetCall.refreshCallStatus(obj);
                            }                            
                            
                            
                        }
                        
                        if(obj.cmd === 'updateIncomingCall') {
                            if (typeof refreshInboxCallWidget === "function") {
                                refreshInboxCallWidget(obj);
                            }
                            if (typeof PhoneWidgetCall === 'object') {
                                if (typeof obj.status !== 'undefined') {
                                     PhoneWidgetCall.requestIncomingCall(obj);
                                }
                                // if (obj.cua_status_id === 2) {
                                    // PhoneWidgetCall.removeIncomingRequest(obj.callSid);
                                // }
                            }
                        }
                        
                        if (obj.cmd === 'clientChatRequest') {
                            if (typeof refreshClientChatWidget === "function") {
                                refreshClientChatWidget(obj);
                            }
                        }
                        
                        
                        if(obj.cmd === 'callMapUpdate') {
                            $('#btn-user-call-map-refresh').click();
                        }
                        
                        if(obj.cmd === 'openUrl') {
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
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                             }
                        }
                        
                        if (obj.cmd === 'muteCall') {
                            if (typeof obj.data !== 'undefined') {
                                muteEvent(obj.data);
                             }
                        }
                        
                        if (obj.cmd === 'missedCall') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                             }
                        }
                        
                        if (obj.cmd === 'removeIncomingRequest') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.removeIncomingRequest(obj.data.call.sid);
                                }
                             }
                        }
                        
                        if (obj.cmd === 'completeCall') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.completeCall(obj.data.call.sid);
                                }
                             }
                        }
                        
                        if (obj.cmd === 'callAlreadyTaken') {
                            createNotify('Accept Call', 'The call has already been taken by another agent', 'warning');
                            if (typeof PhoneWidgetCall === 'object') {
                                PhoneWidgetCall.removeIncomingRequest(obj.callSid);
                            }
                        }

                        if (obj.cmd === 'conferenceUpdate') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                             }
                        }

                        if (obj.cmd === 'clientChatUnreadMessage') {
                        
                            let activeChatId = localStorage.getItem('activeChatId');
                            
                            if (document.visibilityState == "visible" && window.name === 'chat' && activeChatId == obj.data.cchId && obj.data.cchUnreadMessages) {
                                $.post('{$discardUnreadMessageUrl}', {cchId: activeChatId}); 
                                return false;
                            }
                        
                            let previousPage = localStorage.getItem('previousPage');
                            if ((document.visibilityState == "visible") && obj.data.soundNotification && window.name === 'chat') {
                                soundNotification('incoming_message');
                            } else if (previousPage === $(document)[0].baseURI && obj.data.soundNotification) {
                                soundNotification('incoming_message');
                            }
                            
                            if(obj.data.totalUnreadMessages) {
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
                                $("._cc-chat-unread-message").find("[data-cch-id='"+obj.data.cchId+"']").html(obj.data.cchUnreadMessages);
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
                            if($('#notify-pjax-cc').length > 0){
                                pjaxReload({container: '#notify-pjax-cc', url: '{$ccNotificationUpdateUrl}'});
                            }
                            
                            if (obj.data.cchId && obj.data.moment) {
                                let seconds = + obj.data.moment;
                                $("._cc-item-last-message-time[data-cch-id='"+obj.data.cchId+"']").attr('data-moment', obj.data.moment).html(moment.duration(-seconds, 'seconds').humanize(true));
                            }
                        }
                        
                        if (obj.cmd === 'clientChatUpdateItemInfo') {
                            let seconds = + obj.data.moment;
                            $("._cc-item-last-message-time[data-cch-id='"+obj.data.cchId+"']").attr('data-moment', obj.data.moment).html(moment.duration(-seconds, 'seconds').humanize(true));
                            let lastMessageValue = $('#chat-last-message-' + obj.data.cchId);
                            if (lastMessageValue.length > 0) {
                                lastMessageValue.html('<p title="Last ' + obj.data.messageOwner + ' message"><small>' + obj.data.shortMessage + '</small></p>');
                                pushDialogOnTop(obj.data.cchId)
                             }
                        }
                        
                        if (obj.cmd === 'clientChatUpdateClientStatus') {
                            if (obj.cchId) {
                                $('._cc-list-wrapper').find('[data-cch-id="'+obj.cchId+'"]').find('._cc-status').attr('data-is-online', obj.isOnline);
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
                            if (typeof autoLogout === "function") {
                                autoLogout(obj);
                            }
                        }
                        
                        if (obj.cmd === 'updateCurrentCalls') {
                            if (typeof PhoneWidgetCall === "object") {
                                PhoneWidgetCall.updateCurrentCalls(obj.data, obj.userStatus);
                            }
                        }
                        
                        if (obj.cmd === 'addCallToHistory') {
                            if (window.tabHistoryLoaded) {
                                if (typeof PhoneWidgetCall === "object") {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                            } else {
                                console.log('History not loaded.');
                            }
                        }
                        
                        if (obj.cmd === 'showNotification') {
                            let data = obj.data;
                            createNotify(data.title, data.message, data.type);
                        }
                        
                        if (obj.cmd === 'updateVoiceMailRecord') {
                            if ($("#voice-mail-pjax").length > 0) {
                                pjaxReload({container: "#voice-mail-pjax"});    
                            }
                            window.updateVoiceRecordCounters();
                        }
                        
                        if(obj.cmd === 'reloadClientChatList') {
                            if (typeof window.refreshChannelList === 'function') {
                                window.refreshChannelList();
                            }                            
                        }
                        
                        if(obj.cmd === 'reloadChatInfo') {
                            let boxElement = $('#_cc_additional_info_wrapper');
                            if (boxElement.length) {                                
                                if (!('data' in obj)) {
                                    console.error('Error: reloadChatInfo - "data" required in "obj"');
                                    return;
                                }
                                if (!('cchId' in obj.data) || !('message' in obj.data)) {
                                    console.error('Error: reloadChatInfo - "cchId" and "message" required in "obj.data"');
                                    return;
                                }
                            
                                let activeChatId = parseInt(localStorage.getItem('activeChatId'), 10);
                                let cchId = parseInt(obj.data.cchId, 10);
                                
                                if (activeChatId === cchId) {
                                    window.refreshChatInfo(cchId);
                                    createNotify('Warning', obj.data.message, 'warning');
                                }
                            }    
                        }
                        
                        if(obj.cmd === 'clientChatAddOfferButton') {                            
                            let chatId = parseInt(obj.data.chatId, 10);
                            let leadId = parseInt(obj.data.leadId, 10);
                            let content = '<span class="chat-offer" data-chat-id="' + chatId + '" data-lead-id="' + leadId + '"><i class="fa fa-plane"> </i> Offer</span>';
                            $(document).find('span[data-cc-lead-info-offer="' + leadId + '"]').html(content);
                        }
                        
                        if(obj.cmd === 'clientChatRemoveOfferButton') {                            
                            let chatId = parseInt(obj.data.chatId, 10);
                            let leadId = parseInt(obj.data.leadId, 10);
                            $(document).find('span[data-cc-lead-info-offer="' + leadId + '"]').html("");
                        }
                        
                        if (obj.cmd === 'recordingEnable') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                             }
                        }
                        
                        if (obj.cmd === 'recordingDisable') {
                            if (typeof obj.data !== 'undefined') {
                                if (typeof PhoneWidgetCall === 'object') {
                                    PhoneWidgetCall.socket(obj.data);
                                }
                             }
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
                // setTimeout(function() {
                //   wsInitConnect();
                // }, 5000);
                //console.log('Socket Status: ' + socket.readyState + ' (Closed)');
            };
    
            socket.onerror = function(event) {
                //if (socket.readyState == 1) {
                    console.log('Socket error: ' + event.message);
                //}
                onlineObj.attr('title', 'Online Connection: false').find('i').removeClass('success').addClass('danger');
                window.socketConnectionId = null;
                
            };
    
        } catch (error) {
            console.error(error);
            onlineObj.attr('title', 'Online Connection: error').find('i').removeClass('success').addClass('danger');
            window.socketConnectionId = null;
        }
    }
    
    wsInitConnect();

JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'connection-js');


if (UserMonitor::isAutologoutEnabled()) {
    $isAutologoutShowMessage = UserMonitor::isAutologoutShowMessage() ? 'true' : 'false';
    $isAutologoutTimerSec = UserMonitor::isAutologoutTimerSec();

    $js = <<<JS
const isAutologoutShowMessage = $isAutologoutShowMessage;
const isAutologoutTimerSec = $isAutologoutTimerSec;

$('#btn-cancel-autologout').on('click', function () {
     cancelAutoLogout();
});

$('#btn-logout').on('click', function () {
     if (isAutologoutShowMessage) {
        $('#modal-autologout').modal('hide');
     }
     logout();
});

const channel = new BroadcastChannel('tabCommands');

channel.onmessage = function(e) {
    if (e.data.event === 'stopAutoLogout') {
        stopAutoLogout();      
    }
};

function logout() {
    window.location.href = '/site/logout?type=autologout';
}

function cancelAutoLogout() {
    stopAutoLogout();
    channel.postMessage({event: 'stopAutoLogout'});
    return false;
}

function stopAutoLogout() {
    $('#autologout-timer').timer('remove');
    
    if (isAutologoutShowMessage) {
        $('#modal-autologout').modal('hide');
    }
    return false;
}

function autoLogout() {
    // let objDiv = $('#user-monitor-indicator div');
    // objDiv.attr('class', 'text-danger');
    // objDiv.find('i').attr('class', 'fa fa-power-off');
    
    if (isAutologoutTimerSec > 0) {
        $('#autologout-timer').timer('remove').timer({countdown: true, format: '%M:%S', seconds: 0, duration: isAutologoutTimerSec + 's', callback: function() {
            logout();
        }}).timer('start');
    }
    
    // console.log('autoLogout');
    if (isAutologoutShowMessage) {
        $('#modal-autologout').modal({show: true});
    }
}
JS;

    $this->registerJs($js, \yii\web\View::POS_READY, 'autologout-js');
}


if (UserConnection::isIdleMonitorEnabled()) {
    $idleMs = UserConnection::idleSeconds() * 1000;
    $js = <<<JS

function setIdle() {
    let objDiv = $('#user-monitor-indicator div');
    objDiv.attr('class', 'text-warning');
    objDiv.find('i').attr('class', 'fa fa-coffee');
    $('#user-monitor-timer').timer('remove').timer({format: '%M:%S', seconds: 0}).timer('start');
    //console.log('I\'m idle');
}

function setActive() {
    let objDiv = $('#user-monitor-indicator div');
    objDiv.attr('class', 'text-success');
    objDiv.find('i').attr('class', 'fa fa-clock-o');
    $('#user-monitor-timer').timer('remove').text('');//.timer({format: '%M:%S', seconds: 0}).timer('start');
    //console.log('Hey, I\'m active!');
}

$(document).idle({
    onIdle: function(){
        socketSend('idle', 'set', { val: true });
        setIdle();
    },
    onActive: function(){
        socketSend('idle', 'set', { val: false });
        setActive();
    },
    onHide: function(){
        socketSend('window', 'set', { val: false });
        //console.log('I\'m hidden');
    },
    onShow: function(){
        socketSend('window', 'set', { val: true });
        //console.log('Hey, I\'m visible!');
    },
    idle: $idleMs
});
JS;

    $this->registerJs($js, \yii\web\View::POS_READY, 'idle-js');
}
//}

if (\sales\helpers\setting\SettingHelper::isCallRecordingLogEnabled()) {
    $callRecodingLogUrl = Url::to(['/call/call-recording-log']);
    $conferenceRecodingLogUrl = Url::to(['/conference/recording-log']);
    $js = <<<JS
        $(document).ready( function () {
            document.addEventListener('play', function(e) {
                let audioWrapper = $(e.target).closest('.audio-wrapper')[0] || undefined;
                if (e.target.tagName === 'AUDIO' && audioWrapper && audioWrapper.hasAttribute('data-sid')) {
                    let sid = audioWrapper.getAttribute('data-sid');
                    audioWrapper.removeAttribute('data-sid');
                    let isConferenceRecording = audioWrapper.hasAttribute('data-conference-recording');
                    let url = isConferenceRecording ? '$conferenceRecodingLogUrl' : '$callRecodingLogUrl';
                    if (sid) {
                        $.post(url, {sid: sid}, function (data) {
                            if (data.cacheDuration) {
                                setTimeout(function () {
                                    $(audioWrapper).attr('data-sid', sid);                         
                                }, data.cacheDuration*1000);
                            }
                        }, 'json');
                    }
                }
            }, true);
        });
    JS;
    $this->registerJs($js, \yii\web\View::POS_END, 'call-recording-log');
}
