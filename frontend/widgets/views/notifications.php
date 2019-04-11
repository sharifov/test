<?php
/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */
?>
<?php yii\widgets\Pjax::begin(['id' => 'notify-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => [
        'tag' => 'li',
        'class' => 'dropdown',
        'role' => 'presentation',
]])?>
    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-comment-o"></i>
        <?php if($newCount): ?>
            <span class="badge bg-green"><?=$newCount?></span>
        <? endif;?>
    </a>

    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
        <?php

        $soundPlay = false;

        if($model)
            foreach ($model as $n => $item): ?>
        <li>
            <a href="<?=\yii\helpers\Url::to(['notifications/view2', 'id' => $item->n_id])?>" data-pjax="0">
                <span class="glyphicon glyphicon-info-sign"> <?php //remove-sign, ok-sign, question-sign ?>
                </span>
                <span>
                    <span><?=\yii\helpers\Html::encode($item->n_title)?></span>
                    <span class="time"><?=Yii::$app->formatter->asRelativeTime(strtotime($item->n_created_dt))?></span>
                </span>
                <span class="message">
                    <?=\yii\helpers\StringHelper::truncate(\common\models\Email::strip_html_tags($item->n_message), 80, '...');?><br>
                    <?/*=$item->n_created_dt?><br>
                    <?= Yii::$app->formatter->asRelativeTime(strtotime($item->n_created_dt))*/?>
                </span>
            </a>
            <?php
                if($item->n_popup && !$item->n_popup_show):
                $soundPlay = true;

                $message = str_replace("\r\n", '', $item->n_message);
                $message = str_replace("\n", '', $message);
                $message = str_replace('"', '\"', $message);

                $type = $item->getNotifyType();

                if(!$item->n_popup_show) {
                    $item->n_popup_show = true;
                    $item->save();
                }

                $js2 = '
                new PNotify({
                    title: "'.\yii\helpers\Html::encode($item->n_title).'",
                    type: "'.$type.'",
                    text: "'.$message.'",
                    desktop: {
                        desktop: true
                    },
                    /*nonblock: {
                        nonblock: true
                    },*/
                    delay: 30000,
                    hide: true
                }).get().click(function(e) {
        
                });
                
                new PNotify({
                    title: "'.\yii\helpers\Html::encode($item->n_title).'",
                    type: "'.$type.'",
                    text: "'.$message.'",
                    hide: true
                });
                
                ';

                if($n < 20) {
                    $this->registerJs($js2, \yii\web\View::POS_READY);
                }
            ?>
            <? endif;?>
        </li>
        <? endforeach; ?>
        <li>
            <div class="text-center">
                <?=\yii\helpers\Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['notifications/list'], ['data-pjax' => 0])?>
            </div>

            <?php
                if($newCount) {
                    $jsDiv = '<span class="label-success label pull-right">'.$newCount.'</span>';
                    $this->registerJs('favicon.badge('.$newCount.');', \yii\web\View::POS_READY);
                } else {
                    $jsDiv = '';
                    //$this->registerJs('favicon.badge(10);', \yii\web\View::POS_READY);
                    $this->registerJs('favicon.reset();', \yii\web\View::POS_READY);
                }

                $this->registerJs("$('#div-cnt-notification').html('".$jsDiv."'); ", \yii\web\View::POS_READY);
            ?>
        </li>

    </ul>
    <?php
        if($soundPlay) {
            $this->registerJs('ion.sound.play("button_tiny");', \yii\web\View::POS_READY);
        }
    ?>

<?php yii\widgets\Pjax::end() ?>

<?php




/*echo  \yii\helpers\Url::home().'<br>';
echo  \yii\helpers\Url::base().'<br>';

echo  \yii\helpers\Url::home(true).'<br>';
echo  \yii\helpers\Url::base(true).'<br>';

exit;*/

//\yii\helpers\VarDumper::dump($_SERVER, 10, true); exit;

//$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';

$userId = Yii::$app->user->id;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
$pageUrl = urlencode(\yii\helpers\Url::current());
$leadId = null;
$ipAddress = Yii::$app->request->remoteIP;
$webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';
//$userAgent = urlencode(Yii::$app->request->userAgent);

if(Yii::$app->controller->action->uniqueId === 'lead/view') {

    $leadId = Yii::$app->request->get('id');

    if(!$leadId) {
        $gid = Yii::$app->request->get('gid');
        $lead = \common\models\Lead::find()->where(['gid' => $gid])->one();
        if($lead) {
            $leadId = $lead->id;
            unset($lead);
        }
    }
}

$js = <<<JS
    function updatePjaxNotify() {
        //alert('ajax 1');
        $.pjax({container : '#notify-pjax', push: false, timeout: '8000', scrollTo: false});  
    }
    var timerId2 = setInterval(updatePjaxNotify, 3 * 60000);

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
                //do something with socket...
            
                //if you want to use the socket later again and assure that it is still open:
                initWebsocket('ws:\\localhost:8090', socket, 5000, 10).then(function(socket){
                    //if socket is still open, you are using the same "socket" object here
                    //if socket was closed, you are using a new opened "socket" object
                }
            
            }, function(){
                console.log('init of socket failed!');
        });*/
        
        //const socket = new WebSocket('wss:\\sales.dev.travelinsides.com:8888/?user_id=1&controller_id=test&action_id=test&page_url=test&lead_id=15636');
        
        //const socket = new WebSocket('wss:\\sales.dev.travelinsides.com:8888/?user_id=1&controller_id=test&action_id=test&page_url=test&lead_id=15636');
        
        socket.onopen = function (e) {
            //socket.send('{"user2_id":' + user_id + '}');
            //alert(1234);
            console.log('Socket Status: ' + socket.readyState + ' (Open)');
            //console.log(e);
        };
        
        socket.onmessage = function (e) {
 
            //console.log(e.data);
            //alert(e.data);
            //alert(345);
            
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
                        if (typeof callUpdate === "function") {
                            callUpdate(obj);
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
                    
                    if(obj.command === 'incomingCall') {
                        if (typeof incomingCall === "function") {
                            incomingCall(obj);
                        }
                    }
                    
                    if(obj.command === 'updateUserCallStatus') {
                        if (typeof updateUserCallStatus === "function") {
                            updateUserCallStatus(obj);
                        }
                    }
                    
                    if(obj.command === 'callMapUpdate') {
                        $('#btn-user-call-map-refresh').click();
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


