<?php
/* @var $leadId integer */
/* @var $caseId integer */
/* @var $userId integer */
/* @var $controllerId string */
/* @var $actionId string */
/* @var $pageUrl string */
/* @var $ipAddress string */
/* @var $webSocketHost string */
/* @var $subList array */
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
        'controller_id' => $controllerId,
        'action_id' => $actionId,
        'page_url' => $pageUrl,
        'lead_id' => $leadId,
        'case_id' => $caseId,
        'ip'    => $ipAddress,
        'sub_list' => $subList
];

if ($leadId) {
    $urlParams['lead_id'] = $leadId;
}

if ($caseId) {
    $urlParams['case_id'] = $caseId;
}
$urlParamsStr = http_build_query($urlParams);

$wsUrl = $webSocketHost . '/?' . $urlParamsStr;

$js = <<<JS
   
    var socket   = null;

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

    try {

        const socket = new WebSocket(wsUrl);
        
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
                        
                        if (typeof refreshCallBox === "function") {
                            refreshCallBox(obj);
                        }
                        
                        
                    }
                    
                    if(obj.cmd === 'updateIncomingCall') {
                        if (typeof refreshInboxCallWidget === "function") {
                            refreshInboxCallWidget(obj);
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

JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    //if (Yii::$app->controller->module->id != 'user-management') {
$this->registerJs($js, \yii\web\View::POS_READY);
    //}
//}


