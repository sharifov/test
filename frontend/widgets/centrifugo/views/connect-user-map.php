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
        
        //console.log(messageObj.onlineDepSales)
        $("#card-sales-header-count, #card-sales").text('');        
        $("#card-sales-header-count").append(messageObj.onlineDepSales.length);
            messageObj.onlineDepSales.forEach(function (obj, index) {
                index++;
                $("#card-sales").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.onlineDepExchange)
        $("#card-exchange-header-count, #card-exchange").text('');        
         $("#card-exchange-header-count").append(messageObj.onlineDepExchange.length);
            messageObj.onlineDepExchange.forEach(function (obj, index) {
                index++;
                $("#card-exchange").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.onlineDepSupport)
        $("#card-support-header-count, #card-support").text('');        
        $("#card-support-header-count").append(messageObj.onlineDepSupport.length);
            messageObj.onlineDepSupport.forEach(function (obj, index) {
                index++;
                $("#card-support").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.usersOnline)
        $("#card-other").text('');        
            messageObj.usersOnline.forEach(function (obj, index) {
                index++;
                $("#card-other").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });               
        
        console.log(messageObj.realtimeCalls)
        $("#card-live-calls").text('');
            messageObj.realtimeCalls.forEach(function (parentObj, index) {
                if(!parentObj.c_parent_id){
                    $("#card-live-calls").append(renderRealtimeCalls(parentObj));
                    messageObj.realtimeCalls.forEach(function (childObj, childIndex) {
                        if (parentObj.c_id == childObj.c_parent_id){
                            console.log(childObj.c_id)
                            $('#parent-' + parentObj.c_id).append(renderChildCalls(childObj));
                        }
                    });
                }                
            }); 
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

function renderRealtimeCalls(parentObj)
{
    return '<div class="col-md-12" style="margin-bottom:2px">' +
        '<table class="table table-condensed">' +
            '<tbody id=parent-'+ parentObj.c_id +'>' +
                '<tr class="warning">' +
                    '<td>' + 
                        '<u>'+
                            '<a href="/call/view?id='+ parentObj.c_id +'" target="_blank">'+ parentObj.c_id +'</a>' +
                        '</u>' +                  
                    '</td>' +
                '</tr>' +
            '</tbody>' +
        '</table>' +
    '</div>';
}

function renderChildCalls(childObj)
{
    return '<tr class="warning">' +
        '<td>' + 
             '<u>'+
                  '<a href="/call/view?id='+ childObj.c_id +'" target="_blank">'+ childObj.c_id +'</a>' +
              '</u>' +                  
        '</td>' +
    '</tr>';
}

function renderUsersOnline(index, userID, username, userRoles, isCallStatusReady, isCallFree)
{
    let roles = userRoles.split('-');
    let iconClass = 'fa-user';
    let textClass = 'text-danger';
    
    if (roles.includes('admin')){
        iconClass = 'fa-android'
    } else if (roles.includes('qa')){
        iconClass = 'fa-linux'
    } else if (roles.includes('supervision') || roles.includes('sup_super') || roles.includes('ex_super')){
        iconClass = 'fa-user-md'
    }
    
    if (Boolean(Number(isCallStatusReady)) && !Boolean(Number(isCallFree))) {
        textClass = 'text-success'
    } else if (Boolean(Number(isCallStatusReady))){
        textClass = 'text-warning'
    }
    
    return '<div class="col-md-6" style="margin-bottom: 5px">' + 
        index + '. '+
        '<i class="fa '+ iconClass +' fa-lg '+ textClass +'" title="'+ userID +'"></i> ' + username
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
                <div id="card-live-calls" class="card-body">
                   <!-- Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS -->
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

