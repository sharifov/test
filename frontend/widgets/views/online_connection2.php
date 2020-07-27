<?php
/* @var $leadId integer */

use yii\helpers\Url;

/* @var $caseId integer */
/* @var $userId integer */
/* @var $controllerId string */
/* @var $actionId string */
/* @var $pageUrl string */
/* @var $ipAddress string */
/* @var $webSocketHost string */
/* @var $subList array */
/* @var $this \yii\web\View */

\frontend\assets\WebSocketAsset::register($this);

?>
    <li>
        <a href="javascript:;" class="info-number" title="Online Connection" id="online-connection-indicator">
            <i class="fa fa-plug"></i>
            <?php /*//php if($newCount): ?>
                <span class="badge" title="Open tabs"></span>
            <?php //php endif;*/?>
        </a>
    </li>
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

    const userId = '$userId';
    const wsUrl = '$wsUrl';
    const onlineObj = $('#online-connection-indicator');
    
    function wsInitConnect(){
        
        try {
    
            //const socket = new WebSocket(wsUrl);
            var socket = new ReconnectingWebSocket(wsUrl, null, {debug: false, reconnectInterval: 15000});
            window.socket = socket;
            
            socket.onopen = function (e) {
                //socket.send('{"user2_id":' + user_id + '}');
                console.info('Socket Status: ' + socket.readyState + ' (Open)');
                onlineObj.attr('title', 'Online Connection: opened').find('i').removeClass('danger').addClass('warning');
                //console.log(e);
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
                                    notificationInit(obj.notification);
                                 } else {
                                     console.error('connecting user Id not equal notification user Id');
                                 }
                             } else {
                                updatePjaxNotify();    
                             }
                        }
                                            
                        if(obj.cmd === 'updateCommunication') {
                            // updatePjaxNotify();
                            updateCommunication();
                        }
                        
                        if(obj.cmd === 'callUpdate') {
                            /*if (typeof callUpdate === "function") {
                                callUpdate(obj);
                            }*/
                            
                            if (typeof refreshCallBox === "function") {
                                refreshCallBox(obj);
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
                        
                        /*if(obj.cmd === 'incomingCall') {
                            if (typeof incomingCall === "function") {
                                incomingCall(obj);
                            }
                        }*/
                        
                        if(obj.cmd === 'updateUserCallStatus') {
                            /*if (typeof updateUserCallStatus === "function") {
                                updateUserCallStatus(obj);
                            }*/
                            
                             if (typeof PhoneWidgetCall === 'object') {
                                 PhoneWidgetCall.changeStatus(obj.type_id);
                            }
                            
                            if (typeof refreshCallBox === "function") {
                                refreshCallBox(obj);
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
                                if (obj.cua_status_id === 2) {
                                     PhoneWidgetCall.removeIncomingRequest(obj.callSid);
                                }
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
                        
                            if (document.visibilityState == "visible" && obj.data.soundNotification) {
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
                            
                            if (obj.data.cchId) {
                                $("._cc-chat-unread-message").find("[data-cch-id='"+obj.data.cchId+"']").html(obj.data.cchUnreadMessages);
                            }
                            
                            pjaxReload({container: '#notify-pjax-cc', url: '{$ccNotificationUpdateUrl}'});
                        }
                        
                        if (obj.cmd === 'clientChatUpdateClientStatus') {
                            if (obj.cchId) {
                                $('._cc-list-wrapper').find('[data-cch-id="'+obj.cchId+'"]').find('._cc-status').attr('data-is-online', obj.isOnline);
                            }
                            createNotify('Client Chat Notification', obj.statusMessage, obj.isOnline ? 'success' : 'warning');
                        }

                        if (obj.cmd === 'clientChatUpdateTimeLastMessage') {                            
                            if (obj.data.cchId) {                                
                                $("._cc-item-last-message-time[data-cch-id='"+obj.data.cchId+"']").attr('data-moment', obj.data.moment).html(obj.data.dateTime);
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
            };
            
            /*socket.setTimeout(function () {
            console.log('2 seconds passed, closing the socket');
          socket.close();
        }, 2000);*/
    
    
        } catch (error) {
            console.error(error);
            onlineObj.attr('title', 'Online Connection: error').find('i').removeClass('success').addClass('danger');
        }
    }
    
    wsInitConnect();

JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    //if (Yii::$app->controller->module->id != 'user-management') {
$this->registerJs($js, \yii\web\View::POS_READY);
    //}
//}


