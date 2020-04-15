<?php
use common\models\Employee;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'WebSocket Test';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$userId = Yii::$app->user->id;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
$pageUrl = urlencode(\yii\helpers\Url::current());
$ipAddress = Yii::$app->request->remoteIP;
$webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';

?>
<div class="ws-index">
    <h1><?= Html::encode($this->title) ?></h1>


    Username:<br />
    <input id="username" type="text"><button id="btnSetUsername">Set username</button>

    <div id="chat" style="width:400px; height: 250px; overflow: scroll;"></div>

    Message:<br />
    <input id="message" type="text"><button id="btnSend">Send</button>
    <div id="response" style="color:#D00"></div>

</div>

<?php
// $userAgent = urlencode(Yii::$app->request->userAgent);
// const socket = new WebSocket('wss:\\sales.dev.travelinsides.com:8888/?user_id=1&controller_id=test&action_id=test&page_url=test&lead_id=15636');
//
//$js = <<<JS
//
//    //var socket2   = null;
//
//    /**
//     * Send a message to the WebSocket server
//     */
//    function onSendClick() {
//        if (socket2.readyState != socket2.OPEN) {
//            console.error("socket2 is not open: " + socket2.readyState);
//            return;
//        }
//        var msg = document.getElementById("message").value;
//        socket2.send(msg);
//    }
//
//
//    const userId = '$userId';
//    const controllerId = '$controllerId';
//    const actionId = '$actionId';
//    const pageUrl = '$pageUrl';
//    const ipAddress = '$ipAddress';
//    const webSocketHost = '$webSocketHost';
//
//    var onlineObj = $('#online-connection-indicator');
//
//    try {
//
//        //const socket2 = new WebSocket(webSocketHost + '/?user_id=' + userId + '&controller_id=' + controllerId + '&action_id=' + actionId + '&page_url=' + pageUrl + '&ip=' + ipAddress);
//        const socket2 = new WebSocket('http://127.0.0.1:8721/rpc?p={%22jsonrpc%22:%222.0%22,%22id%22:34,%22method%22:%22room/msg%22,%22params%22:{%22id%22:%22100111%22,%22content%22:{%22text%22:%22System%20warning!%22}}}');
//
//        /*initWebsocket('ws:\\localhost:8090', null, 5000, 10).then(function(socket){
//                console.log('socket initialized!');
//                initWebsocket('ws:\\localhost:8090', socket, 5000, 10).then(function(socket){
//                }
//
//            }, function(){
//                console.log('init of socket failed!');
//        });*/
//
//
//        socket2.onopen = function (e) {
//            //socket2.send('{"user2_id":' + user_id + '}');
//            console.info('socket2 Status: ' + socket2.readyState + ' (Open)');
//
//            //console.log(e);
//        };
//
//        socket2.onmessage = function (e) {
//            // onlineObj.find('i').removeClass('danger').removeClass('success').addClass('warning');
//            console.info('socket2.onmessage');
//            try {
//                var obj = JSON.parse(e.data); // $.parseJSON( e.data );
//                console.log(obj);
//            } catch (error) {
//                console.error('Invalid JSON data on socket2.onmessage');
//                console.error(e.data);
//            }
//
//            try {
//                if (typeof obj.command !== 'undefined') {
//
//                    if(obj.command === 'initConnection') {
//                        if (typeof obj.uc_id !== 'undefined') {
//                            if(obj.uc_id > 0) {
//                                onlineObj.attr('title', 'Online Connection (' + obj.uc_id +'): true').find('i').removeClass('warning').removeClass('danger').addClass('success');
//                            } else {
//                                onlineObj.attr('title', 'Timeout DB connection: restart service').find('i').removeClass('danger').removeClass('success').addClass('warning');
//                            }
//                        }
//                    }
//
//                }
//                // onlineObj.find('i').removeClass('danger').removeClass('warning').addClass('success');
//            } catch (error) {
//                console.error('Error in functions - socket2.onmessage');
//                console.error(error);
//            }
//
//        };
//
//        socket2.onclose = function (event) {
//
//            if (event.wasClean) {
//                console.log('Connection closed success (Close)');
//            } else {
//                console.error('Disconnect (Error)'); // Example kill process of server
//            }
//            //console.log('Code: ' + event.code);
//
//            onlineObj.attr('title', 'Disconnect').find('i').removeClass('success').addClass('danger');
//            //console.log('socket2 Status: ' + socket2.readyState + ' (Closed)');
//        };
//
//        socket2.onerror = function(event) {
//            //if (socket2.readyState == 1) {
//                console.log('socket2 error: ' + event.message);
//            //}
//            onlineObj.attr('title', 'Online Connection: false').find('i').removeClass('success').addClass('danger');
//        };
//
//        /*socket2.setTimeout(function () {
//        console.log('2 seconds passed, closing the socket2');
//      socket2.close();
//    }, 2000);*/
//
//
//    } catch (error) {
//        console.error(error);
//    }
//
//JS;


$js = <<<JS
var chat = new WebSocket('wss://sales.zeit.test/ws/');
    chat.onmessage = function(e) {
        
        
        
        $('#response').text('');

        var response = JSON.parse(e.data);
        
        console.log(response);
        
        if (response.type && response.type == 'chat') {
            $('#chat').append('<div><b>' + response.from + '</b>: ' + response.message + '</div>');
            $('#chat').scrollTop = $('#chat').height;
        } else if (response.message) {
            $('#response').text(response.message);
        }
    };
    
        chat.onopen = function(e) {
            $('#response').text("Connection established! Please, set your username.");
            console.info('-- Connected --');
        };
    
    
        chat.onclose = function (event) {

           if (event.wasClean) {
               console.log('Connection closed success (Close)');
           } else {
               console.warn('-- Disconnected --'); // Example kill process of server
           }
           //console.log('Code: ' + event.code);

           //onlineObj.attr('title', 'Disconnect').find('i').removeClass('success').addClass('danger');
           //console.log('socket2 Status: ' + socket2.readyState + ' (Closed)');
       };

       chat.onerror = function(event) {
           if (chat.readyState == 1) {
               console.error('chat error: ' + event.message);
           }
       };   
    
    
    $('#btnSend').click(function() {
        if ($('#message').val()) {
            chat.send( JSON.stringify({'action' : 'chat', 'message' : $('#message').val()}) );
        } else {
            alert('Enter the message')
        }
    })

    $('#btnSetUsername').click(function() {
        if ($('#username').val()) {
            chat.send( JSON.stringify({'action' : 'setName', 'name' : $('#username').val()}) );
        } else {
            alert('Enter username')
        }
    })
JS;


$this->registerJs($js, \yii\web\View::POS_READY);
