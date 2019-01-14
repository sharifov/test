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
        <?

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
                    hide: false
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
            $this->registerJs('ion.sound.play("door_bell");', \yii\web\View::POS_READY);
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
$webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';

if(Yii::$app->controller->action->uniqueId === 'lead/view') {
    $leadId = Yii::$app->request->get('id');
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
    var webSocketHost = '$webSocketHost';
    
    /**
     * inits a websocket by a given url, returned promise resolves with initialized websocket, rejects after failure/timeout.
     *
     * @param url the websocket url to init
     * @param existingWebsocket if passed and this passed websocket is already open, this existingWebsocket is resolved, no additional websocket is opened
     * @param timeoutMs the timeout in milliseconds for opening the websocket
     * @param numberOfRetries the number of times initializing the socket should be retried, if not specified or 0, no retries are made
     *        and a failure/timeout causes rejection of the returned promise
     * @return {Promise}
     */
    function initWebsocket(url, existingWebsocket, timeoutMs, numberOfRetries) {
        timeoutMs = timeoutMs ? timeoutMs : 1500;
        numberOfRetries = numberOfRetries ? numberOfRetries : 0;
        var hasReturned = false;
        var promise = new Promise((resolve, reject) => {
            setTimeout(function () {
                if(!hasReturned) {
                    console.info('opening websocket timed out: ' + url);
                    rejectInternal();
                }
            }, timeoutMs);
            if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
                if (existingWebsocket) {
                    existingWebsocket.close();
                }
                var websocket = new WebSocket(url);
                websocket.onopen = function () {
                    if(hasReturned) {
                        websocket.close();
                    } else {
                        console.info('websocket to opened! url: ' + url);
                        resolve(websocket);
                    }
                };
                websocket.onclose = function () {
                    console.info('websocket closed! url: ' + url);
                    rejectInternal();
                };
                websocket.onerror = function () {
                    console.info('websocket error! url: ' + url);
                    rejectInternal();
                };
            } else {
                resolve(existingWebsocket);
            }
    
            function rejectInternal() {
                if(numberOfRetries <= 0) {
                    reject();
                } else if(!hasReturned) {
                    hasReturned = true;
                    console.info('retrying connection to websocket! url: ' + url + ', remaining retries: ' + (numberOfRetries-1));
                    initWebsocket(url, null, timeoutMs, numberOfRetries-1).then(resolve, reject);
                }
            }
        });
        promise.then(function () {hasReturned = true;}, function () {hasReturned = true;});
        return promise;
    };
    

    try {
        
        
        
        

        const socket = new WebSocket(webSocketHost + '/?user_id=' + userId + '&controller_id=' + controllerId + '&action_id=' + actionId + '&page_url=' + pageUrl + '&lead_id=' + leadId);
        
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
                
                if (typeof obj.command !== 'undefined') {
                    
                    if(obj.command === 'getNewNotification') {
                        //alert(obj.command);
                        updatePjaxNotify();
                    }
                    
                    if(obj.command === 'updateCommunication') {
                        updatePjaxNotify();
                        updateCommunication();
                    }
                }
            } catch (error) {
                console.error('Invalid JSON data');
            }
            
        };

        socket.onclose = function (event) {
            
            if (event.wasClean) {
                console.log('Connection closed success (Close)');
            } else {
                console.error('Обрыв соединения'); // например, "убит" процесс сервера
            }
            console.log('Code: ' + event.code + ', причина: ' + event.reason);
            
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


