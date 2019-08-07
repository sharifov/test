<?php
/* @var $leadId integer */

//$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';

$userId = Yii::$app->user->id;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
$pageUrl = urlencode(\yii\helpers\Url::current());
$ipAddress = Yii::$app->request->remoteIP;
$webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';
?>
    <li>
        <a href="javascript:;" title="Online Connection" id="online-connection-indicator">
            <i class="fa fa-plug"></i>
            <?/*php if($newCount): ?>
                <span class="badge bg-green"><?=$newCount?></span>
            <?php endif;*/?>
        </a>
    </li>
<?php
// $userAgent = urlencode(Yii::$app->request->userAgent);
// const socket = new WebSocket('wss:\\sales.dev.travelinsides.com:8888/?user_id=1&controller_id=test&action_id=test&page_url=test&lead_id=15636');

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


    var userId = '$userId';
    var controllerId = '$controllerId';
    var actionId = '$actionId';
    var pageUrl = '$pageUrl';
    var leadId = '$leadId';
    var ipAddress = '$ipAddress';
    var webSocketHost = '$webSocketHost';
          
    var onlineObj = $('#online-connection-indicator');

    try {

        const socket = new WebSocket(webSocketHost + '/?user_id=' + userId + '&controller_id=' + controllerId + '&action_id=' + actionId + '&page_url=' + pageUrl + '&lead_id=' + leadId + '&ip=' + ipAddress);
        
        /*initWebsocket('ws:\\localhost:8090', null, 5000, 10).then(function(socket){
                console.log('socket initialized!');
                initWebsocket('ws:\\localhost:8090', socket, 5000, 10).then(function(socket){
                }
            
            }, function(){
                console.log('init of socket failed!');
        });*/
        
        
        socket.onopen = function (e) {
            //socket.send('{"user2_id":' + user_id + '}');
            console.info('Socket Status: ' + socket.readyState + ' (Open)');
            onlineObj.attr('title', 'Online Connection: opened').find('i').removeClass('danger').addClass('warning');
            //console.log(e);
        };
        
        socket.onmessage = function (e) {
            onlineObj.find('i').removeClass('danger').removeClass('success').addClass('warning');
            try {
                var obj = JSON.parse(e.data); // $.parseJSON( e.data );
                console.log(obj);
            } catch (error) {
                console.error('Invalid JSON data on socket.onmessage');
                console.error(e.data);
            }
            
            try {
                if (typeof obj.command !== 'undefined') {
                    
                    if(obj.command === 'initConnection') {
                        if (typeof obj.uc_id !== 'undefined') {
                            if(obj.uc_id > 0) {
                                onlineObj.attr('title', 'Online Connection (' + obj.uc_id +'): true').find('i').removeClass('warning').removeClass('danger').addClass('success');
                            }    
                        }
                    }
                    
                    if(obj.command === 'getNewNotification') {
                        //alert(obj.command);
                        updatePjaxNotify();
                    }
                    
                    if(obj.command === 'updateCommunication') {
                        updatePjaxNotify();
                        updateCommunication();
                    }
                    
                    if(obj.command === 'callUpdate') {
                        /*if (typeof callUpdate === "function") {
                            callUpdate(obj);
                        }*/
                        
                        if (typeof refreshCallBox === "function") {
                            refreshCallBox(obj);
                        }
                    }
                    
                    if(obj.command === 'webCallUpdate') {
                        //console.info('webCallUpdate - 1');
                        if (typeof webCallUpdate === "function") {
                            //console.info('webCallUpdate - 2');
                            webCallUpdate(obj);
                        }
                        
                        
                    }
                    
                    if(obj.command === 'recordingUpdate') {
                        updatePjaxNotify();
                        updateCommunication();
                    }
                    
                    /*if(obj.command === 'incomingCall') {
                        if (typeof incomingCall === "function") {
                            incomingCall(obj);
                        }
                    }*/
                    
                    if(obj.command === 'updateUserCallStatus') {
                        /*if (typeof updateUserCallStatus === "function") {
                            updateUserCallStatus(obj);
                        }*/
                        
                        if (typeof refreshCallBox === "function") {
                            refreshCallBox(obj);
                        }
                        
                        
                    }
                    
                    if(obj.command === 'callMapUpdate') {
                        $('#btn-user-call-map-refresh').click();
                    }
                    
                    if(obj.command === 'openUrl') {
                        window.open(obj.url, 'openUrl');
                    }
                }
                onlineObj.find('i').removeClass('danger').removeClass('warning').addClass('success');
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
            console.log('Code: ' + event.code);
            
            onlineObj.attr('title', 'Online Connection: close').find('i').removeClass('success').addClass('info');
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

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


