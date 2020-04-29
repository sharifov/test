<?php
use yii\helpers\Html;

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 */

$passChannelsToJs ='["' . implode('", "', $channels) . '"]';

$js = <<<JS
let channels = $passChannelsToJs;
var centrifuge = new Centrifuge('$centrifugoUrl');
centrifuge.setToken('$token');

channels.forEach(channelConnector)

function channelConnector(chName)
{    
    centrifuge.subscribe(chName, function(message) {    
        let messageObj = JSON.parse(message.data.message);        
        //console.log(messageObj)
        
        console.log(messageObj.onlineDepSales)
        $("#card-sales-header-count, #card-sales").text('');        
        $("#card-sales-header-count").append(messageObj.onlineDepSales.length);
            messageObj.onlineDepSales.forEach(function (obj, index) {
                index++;
                $("#card-sales").append(renderUsersOnline(index, obj.uc_user_id, obj.username));
            });        
        
        console.log(messageObj.onlineDepExchange)
        $("#card-exchange-header-count, #card-exchange").text('');        
         $("#card-exchange-header-count").append(messageObj.onlineDepExchange.length);
            messageObj.onlineDepExchange.forEach(function (obj, index) {
                index++;
                $("#card-exchange").append(renderUsersOnline(index, obj.uc_user_id, obj.username));
            });        
        
        console.log(messageObj.onlineDepSupport)
        $("#card-support-header-count, #card-support").text('');        
        $("#card-support-header-count").append(messageObj.onlineDepSupport.length);
            messageObj.onlineDepSupport.forEach(function (obj, index) {
                index++;
                $("#card-support").append(renderUsersOnline(index, obj.uc_user_id, obj.username));
            });        
        
        console.log(messageObj.usersOnline)
        $("#card-other").text('');        
            messageObj.usersOnline.forEach(function (obj, index) {
                index++;
                $("#card-other").append(renderUsersOnline(index, obj.uc_user_id, obj.username));
            });               
        
        //console.log(messageObj.realtimeCalls)
    });
}
centrifuge.connect();

centrifuge.on('connect', function(context) {        
    //console.info('Client connected to Centrifugo and authorized')
    $.ajax({
        url: '/call/realtime-user-map',
        type: 'POST',
        success: function(data) { 
            //console.info('Request data on connect');                 
        }
     });
});

function renderUsersOnline(index, userID, username)
{
    return '<div class="col-md-6" style="margin-bottom: 5px">' + 
        index + '. '+
        '<i class="fa fa-user fa-lg text-danger" title="'+ userID +'"></i> ' + username
    '</div>';    
}

$('#btn-user-call-map-refresh').on('click', function () {
        $.ajax({
        url: '/call/realtime-user-map',
        type: 'POST',
        success: function(data) { 
            //console.info('Request data on click Refresh Data');                 
        }
     });
});

JS;

$this->registerJs($js);
?>

<div id="call-map-page" class="col-md-12">
    <div class="row">
        <div class="col-md-2">
            <div class="card card-default" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SALES (<span id="card-sales-header-count"></span>)</div>
                <div id="card-sales" class="card-body">
                    <!-- User List: from SALES department -->
                </div>
            </div>

            <div class="card card-default" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department EXCHANGE (<span id="card-exchange-header-count"></span>)</div>
                <div id="card-exchange" class="card-body">
                   <!-- User List: from EXCHANGE department -->
                </div>
            </div>

            <div class="card card-default" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SUPPORT (<span id="card-support-header-count"></span>)</div>
                <div id="card-support" class="card-body">
                    <!-- User List: from SUPPORT department -->
                </div>
            </div>

            <div class="card card-default" style="margin-bottom: 20px;">
                <!--<div class="card-header"><i class="fa fa-users"></i> OnLine Users - W/O Department (1)</div>-->
                <div id="card-other" class="card-body">
                    <!-- User List: from W/O department -->
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"><i class="fa fa-list"></i> Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS (Updated: <i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asTime(time(), 'php:H:i:s') ?>)</div>
                <div class="card-body">
                    Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"><i class="fa fa-list"></i> Last 10 ended Calls</div>
                <div class="card-body">
                    Last 10 ended Calls
                </div>
            </div>
        </div>

    </div>

    <div class="text-center hidden">
        <?=Html::button('Refresh Data', ['class' => 'btn btn-sm btn-success hidden', 'id' => 'btn-user-call-map-refresh'])?>
    </div>
</div>

