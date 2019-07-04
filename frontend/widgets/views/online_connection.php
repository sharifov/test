<?php
/* @var $leadId integer */

//$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';

$userId = Yii::$app->user->id;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
$pageUrl = urlencode(\yii\helpers\Url::current());
$ipAddress = Yii::$app->request->remoteIP;
$webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';

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
            console.log('Socket Status: ' + socket.readyState + ' (Open)');
            //console.log(e);
        };
        
        socket.onmessage = function (e) {
            
            try {
                var obj = JSON.parse(e.data); // $.parseJSON( e.data );
                console.log(obj);
            } catch (error) {
                console.error('Invalid JSON data on socket.onmessage');
                console.error(e.data);
            }
            
            try {
                
                if (typeof obj.command !== 'undefined') {
                    
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
            
            //console.log('Socket Status: ' + socket.readyState + ' (Closed)');
        };

        socket.onerror = function(event) {
            //if (socket.readyState == 1) {
                console.log('Socket error: ' + event.message);
            //}
        };
        
        /*socket.setTimeout(function () {
        console.log('2 seconds passed, closing the socket');
      socket.close();
    }, 2000);*/


    } catch (error) {
        console.error(error);
    }

JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


