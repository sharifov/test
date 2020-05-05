<?php
use yii\helpers\Html;
use \common\models\Call;
use common\models\CallUserAccess;

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 */
$this->title = 'Real-time User Call Map';

$cIn = Call::CALL_TYPE_IN;
$cOut = Call::CALL_TYPE_OUT;
$sourceGeneralLine = Call::SOURCE_GENERAL_LINE;
$shotSourceList = json_encode(Call::SHORT_SOURCE_LIST);
$statusList = json_encode(Call::STATUS_LIST);
$callIsEnded = json_encode([
        Call::STATUS_COMPLETED,
        Call::STATUS_BUSY,
        Call::STATUS_NO_ANSWER,
        Call::STATUS_CANCELED,
        Call::STATUS_FAILED,
]);

$pending = CallUserAccess::STATUS_TYPE_PENDING;
$accept = CallUserAccess::STATUS_TYPE_ACCEPT;
$busy = CallUserAccess::STATUS_TYPE_BUSY;

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
        
        //console.log(messageObj.realtimeCalls)
        $("#card-live-calls").text('');
            messageObj.realtimeCalls.forEach(function (parent, index) {
                if(!parent.c_parent_id){
                    $("#card-live-calls").append(renderRealtimeCalls(parent));
                    messageObj.realtimeCalls.forEach(function (child, childIndex) {
                        if (parent.c_id == child.c_parent_id){                            
                            $('#parent-' + parent.c_id).append(renderChildCalls(child));
                        }
                    });
                }                
            });
            
        console.log(messageObj.callsHistory)
        $("#card-history-calls").text('');
            messageObj.callsHistory.forEach(function (parent, index) {
                if(!parent.c_parent_id){
                    $("#card-history-calls").append(renderRealtimeCalls(parent));
                    messageObj.callsHistory.forEach(function (child, childIndex) {
                        if (parent.c_id == child.c_parent_id){                            
                            $('#parent-' + parent.c_id).append(renderChildCalls(child));
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

function renderRealtimeCalls(parent)
{
    return '<div class="col-md-12" style="margin-bottom:2px">' +
        '<table class="table table-condensed">' +
            '<tbody id=parent-'+ parent.c_id +'>' +
                '<tr class="warning">' +                    
                    '<td class="text-center" style="width:50px">' + 
                        '<u>'+
                            '<a href="/call/view?id='+ parent.c_id +'" target="_blank">'+ parent.c_id +'</a>' +
                        '</u>' + isCallInOrOut(parent.c_call_type_id, parent.c_parent_id) +                  
                    '</td>' +
                    '<td class="text-left" style="width:180px">' +                        
                        showAgentClientDetails(parent.full_name, parent.c_call_type_id, parent.c_from, parent.c_created_user_id, parent.username) + 
                    '</td>' + 
                    '<td class="text-center" style="width:130px">' +
                        '<span class="badge badge-info">'+ parent.project_name +'</span><br>' +
                        '<span class="label label-warning">'+ parent.dep_name +'</span>' + 
                        '<span class="label label-info">'+ showShortSource(parent.c_source_type_id) +'</span>' +            
                    '</td>' + 
                    '<td class="text-left">' +
                        showLeadCaseLinks(parent.c_lead_id, parent.gid, parent.c_case_id, parent.cs_gid) +      
                    '</td>' + 
                    '<td class="text-center">' + 
                        showCurrentStatus(parent.c_status_id) +    
                    '</td>' + 
                    '<td class="text-center" style="width:160px">' + 
                        callCreatedTime(parent.c_created_dt, parent.c_status_id, true) +
                    '</td>' +    
                    '<td class="text-left" style="width:160px">' + 
                        showAgentClientInfo(parent.c_call_type_id, parent.c_source_type_id, parent.c_to, parent.c_created_user_id, parent.username, parent.full_name) +
                    '</td>' +    
                '</tr>' + 
                    renderCallUserAccesses(parent.cua_status_ids, parent.cua_user_ids, parent.cua_user_names) +
            '</tbody>' +
        '</table>' +
    '</div>';
}

function renderChildCalls(child)
{
    return '<tr class="warning">' +
        '<td colspan="7">'+
            '<table class="table table-condensed">' +
                '<tbody>' +
                    '<tr>' +
                        '<td style="width:70px; border: none">' + 
                             '<u>'+
                                  '<a href="/call/view?id='+ child.c_id +'" target="_blank">'+ child.c_id +'</a>' +
                              '</u>' + isCallInOrOut(child.c_call_type_id, child.c_parent_id) +                
                        '</td>' +
                        '<td style="width:50px">' +
                             childCallSourceDep(child.c_source_type_id, child.dep_name) +
                        '</td>' +
                        '<td style="width: 120px">' +
                            showCurrentStatus(child.c_status_id) +
                        '</td>' +
                        '<td style="width: 80px" class="text-left"> TIMER' +
                        '</td>' +
                        '<td class="text-left">' +
                            renderChildCallUserAccesses(child.cua_status_ids, child.cua_user_ids, child.cua_user_names) +
                        '</td>' +
                        '<td class="text-center" style="width:90px">' +
                            callCreatedTime(child.c_created_dt, false, false) +
                        '</td>' +
                        '<td class="text-center" style="width:180px">' +
                            childCallRelativeTime(child.c_updated_dt, child.c_status_id) +
                        '<td>' +
                        '<td class="text-left" style="width:130px">' +
                            childCallCreatedUser(child.c_call_type_id, child.c_created_user_id, child.username, child.c_to) +
                        '</td>'
                    '</tr>'   
                '</tbody>' +    
            '</table>' +    
         '</td>'   
    '</tr>';
}

function renderChildCallUserAccesses(statusIDs, userIDs, userNames) {
    let html = '';    
    if(userIDs){
        let userIDsArray = userIDs.split('-');
        let statusIDsArray = statusIDs.split('-');
        let userNamesArray = userNames.split('-');
        
        userIDsArray.forEach(function (user, index){
            let label;
            switch (statusIDsArray[index]) {
                case '$pending':
                    label = 'warning';
                    break;
                case '$accept':
                    label = 'success';
                    break;
                case '$busy':
                    label = 'danger';
                    break;
                default:
                    label = 'default';
            }
            html+='<span class="label label-'+ label +'"><i class="fa fa-user"></i> '+ userNamesArray[index] +'</span>'+' ';   
        });
    }  
  
    return html
}

function renderCallUserAccesses(statusIDs, userIDs, userNames) {
    let html = '';    
    if(userIDs){
        let userIDsArray = userIDs.split('-');
        let statusIDsArray = statusIDs.split('-');
        let userNamesArray = userNames.split('-');
        
        html+= '<tr class="warning">' +
                    '<td class="text-center"><i class="fa fa-users"></i> </td>' +
                    '<td colspan="6">';
        
        userIDsArray.forEach(function (user, index){
            let label;
            switch (statusIDsArray[index]) {
                case '$pending':
                    label = 'warning';
                    break;
                case '$accept':
                    label = 'success';
                    break;
                case '$busy':
                    label = 'danger';
                    break;
                default:
                    label = 'default';
            }
            html+='<span class="label label-'+ label +'"><i class="fa fa-user"></i> '+ userNamesArray[index] +'</span>'+' ';   
        });        
        
        html+= '</td></tr>';
    }  
  
    return html
}

function childCallCreatedUser(callTypeId, callCreatedUserId, callCreatedUsername, callTo){
    let html = '';
    if (callTypeId == '$cIn'){
        html+='<div>'
        if (callCreatedUserId){
            html+='<i class="fa fa-user fa-border"></i> ' + callCreatedUsername     
        } else {
            html+='<i class="fa fa-user fa-border"></i> ' + callTo
        }
        html+='</div>'
    } else {
        html+='<div>'
        if (callCreatedUserId){
            html+='<i class="fa fa-user fa-border"></i> ' + callCreatedUsername     
        } else {
            html+='<i class="fa fa-user fa-border"></i> ' + callTo
        }
        html+='</div>'     
    }
    return html;
}

function childCallRelativeTime(callUpdatedDate, cStatusID) {
    let html = '';
    let considerCallEnded = '$callIsEnded';
    if (callUpdatedDate){
        if (Object.values(considerCallEnded).includes(cStatusID)){
            html+= '<small>'+ calculateRelativeTime(callUpdatedDate) + '</small>';
        }
    }
    return html;
}

function childCallSourceDep(callSourceTypeId, depName){    
    let html = '';
    if (callSourceTypeId) {
        html+= '<span class="label label-info">'+ showShortSource(callSourceTypeId) +'</span>';
    }
    //if (depName){
        html+= '<span class="label label-warning">'+ depName +'</span>';
    /*} else {
        html+= '<span class="label label-warning"> - </span>';
    }*/
    
    return html
}

function showAgentClientInfo(callTypeId, callSourceTypeId, callTo, callCreatedUserId, callCreatedUsername, fullName) {
    let html = '';
    let clientName;
    if (callTypeId == '$cIn'){
        if (callSourceTypeId == '$sourceGeneralLine'){
            html+= '<i class="fa fa-fax fa-1x fa-border"></i> ' + callTo
        }
        if (callCreatedUserId){
             html+= '<i class="fa fa-user fa-1x fa-border"></i> ' + callCreatedUsername
        } else {
            html+= '<i class="fa fa-phone fa-1x fa-border"></i> ' + callTo
        }
        
    } else {
        if (fullName){
            if(fullName.toString().trim() === 'ClientName'){
                clientName = "...";
            } else {
                clientName = fullName
            }               
        } else {
            clientName = "...";
        }
        
        html+= '<div class="col-md-3">' +
                    '<i class="fa fa-male text-info fa-2x fa-border"></i>' +
               '</div>' +
               '<div class="col-md-9">' + clientName + '<br><small>' + callTo + '</small>' +
               '</div>';
    }
    return html;
}

function callCreatedTime(callCreatedDate, cStatusID, enableSeconds) {
    let html = '';
    let date = new Date(callCreatedDate);
    let timeStamp = date.getTime();
    let formattedTime = formatTime(new Date(timeStamp), enableSeconds);
    let considerCallEnded = '$callIsEnded';    
    
    html+= '<i class="fa fa-clock-o"></i> '+ formattedTime +'<br>'
    
    if (Object.values(considerCallEnded).includes(cStatusID)){
        html+= calculateRelativeTime(callCreatedDate)
    }    
    
    return html;
}

function formatTime(date, includeSeconds) {
    let time;
    time = (date.getHours() < 10 ? '0' : '') + date.getHours() + ':' + 
            (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
    if (includeSeconds){
        time+= ':' + (date.getSeconds() < 10 ? '0' : '') + date.getSeconds();
    }
    return  time;
}

function calculateRelativeTime(date) {
    let current = new Date();
    let previous = new Date(date).getTime();
    let msPerMinute = 60 * 1000;
    let msPerHour = msPerMinute * 60;
    let msPerDay = msPerHour * 24;
    let msPerMonth = msPerDay * 30;
    let msPerYear = msPerDay * 365;

    let elapsed = current - previous;

    if (elapsed < msPerMinute) {
        return Math.round(elapsed/1000) + ' seconds ago';
    } else if (elapsed < msPerHour) {
        return Math.round(elapsed/msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay ) {
        return Math.round(elapsed/msPerHour ) + ' hours ago';
    } else if (elapsed < msPerMonth) {
        return Math.round(elapsed/msPerDay) + ' days ago';
    } else if (elapsed < msPerYear) {
        return Math.round(elapsed/msPerMonth) + ' months ago';
    } else {
        return Math.round(elapsed/msPerYear ) + ' years ago';
    }
}

function showCurrentStatus(cStatusID) {
    let statusNames =  JSON.parse('$statusList')
    let icon = '';
    if (Object.keys(statusNames).includes(cStatusID)){        
        
        if (statusNames[cStatusID] === 'Ringing') {
            icon = 'fa fa-refresh fa-pulse fa-fw text-danger';
        } else if (statusNames[cStatusID] === 'In progress') {
            icon = 'fa fa-spinner fa-pulse fa-fw';
        } else if (statusNames[cStatusID] === 'Queued') {
            icon = 'fa fa-pause';
        } else if (statusNames[cStatusID] === 'Completed') {
            icon = 'fa fa-flag text-success';
        } else if (statusNames[cStatusID] === 'Delay') {
            icon = 'fa fa-pause text-success';
        } else if (statusNames[cStatusID] === 'Canceled' || statusNames[cStatusID] === 'No answer' || statusNames[cStatusID] === 'Busy' || statusNames[cStatusID] === 'Failed') {
            icon = 'fa fa-times-circle text-danger';
        }

        return '<i class="'+ icon +'"></i> '+ statusNames[cStatusID] +'<br>';
    }
}

function showLeadCaseLinks(cLeadID, leadGid, cCaseID, caseGid) {
    let html = '';
    
    if (cLeadID && leadGid){
        html += '<i>l:<a href="/lead/view/'+ leadGid +'" target="_blank">'+ cLeadID +'</a></i><br>'
    }
    
    if (cCaseID && caseGid){
        html += '<i>c:<a href="/cases/view/'+ caseGid +'" target="_blank">'+ cCaseID +'</a></i><br>'
    }
    
    return html
}

function showShortSource(callSourceTypeId) {
    let sourceNames = JSON.parse('$shotSourceList')    
    if (Object.keys(sourceNames).includes(callSourceTypeId)){
        return sourceNames[callSourceTypeId]
    }  
}

function isCallInOrOut(callTypeId, callParentID) {   
    if(callTypeId == '$cOut'){        
        if(callParentID){
            return '<br><span class="badge badge-danger">Out</span>'
        } else {
            return '<br><span class="badge badge-blue">Out</span>'
        }        
    } else if(callTypeId == '$cIn'){
        return '<br><span class="badge badge-danger">In</span>'
    }
}

function showAgentClientDetails(fullName, callTypeId, callFrom, callCreatedUserId, callCreatedUsername)
{  
    let html = '';
    let clientName;
    if (callTypeId == '$cIn'){
        if (fullName){
            if(fullName.toString().trim() === 'ClientName'){
                clientName = "...";
            } else {
                clientName = fullName
            }               
        } else {
            clientName = "...";
        }
        
        html+= '<div class="col-md-3">' +
                    '<i class="fa fa-male text-info fa-2x fa-border"></i>' +
               '</div>' +
               '<div class="col-md-9">' + clientName + '<br>' + callFrom +
               '</div>';
    } else {
        if (callCreatedUserId) {
            html+= '<i class="fa fa-user fa-2x fa-border"></i>' + callCreatedUsername
        } else {
            html+= '<i class="fa fa-phone fa-2x fa-border"></i>' + callFrom
        }
    }
    
    return html    
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
<style>
    #call-map-page table {margin-bottom: 5px}
</style>
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
                <div id="card-history-calls" class="card-body">
                    <!--Last 10 ended Calls-->
                </div>
            </div>
        </div>

    </div>

    <div class="text-center hidden">
        <?=Html::button('Refresh Data', ['class' => 'btn btn-sm btn-success hidden', 'id' => 'btn-user-call-map-refresh'])?>
    </div>
</div>

