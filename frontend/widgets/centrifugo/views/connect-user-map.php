<?php
use yii\helpers\Html;
use \common\models\Call;
use common\models\CallUserAccess;

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 * @var $this yii\web\View
 */
$this->title = 'Real-time User Call Map';
$bundle = \frontend\assets\TimerAsset::register($this);
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
        let depSalesIsEmpty = messageObj.onlineDepSales.length;
        let depExchangeIsEmpty = messageObj.onlineDepExchange.length;
        let depSupportIsEmpty = messageObj.onlineDepSupport.length;
        let usersOnlineIsEmpty = messageObj.usersOnline.length;
        
        //console.log(messageObj.onlineDepSales)
        $("#card-sales-header-count, #card-sales").text('');
            if (depSalesIsEmpty > 0){
                $("#card-sales-header-count").append(depSalesIsEmpty);
                $('#sales-department').removeClass('d-none')
            } else {
                $('#sales-department').addClass('d-none')
            }
            
            messageObj.onlineDepSales.forEach(function (obj, index) {
                index++;
                $("#card-sales").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.onlineDepExchange)
        $("#card-exchange-header-count, #card-exchange").text('');
            if (depExchangeIsEmpty > 0){
                $("#card-exchange-header-count").append(depExchangeIsEmpty);
                $('#exchange-department').removeClass('d-none')
            } else {
                $("#exchange-department").addClass('d-none')
            }
        
            messageObj.onlineDepExchange.forEach(function (obj, index) {
                index++;
                $("#card-exchange").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.onlineDepSupport)
        $("#card-support-header-count, #card-support").text('');
            if (depSupportIsEmpty > 0){
                $("#card-support-header-count").append(depSupportIsEmpty);
                $('#support-department').removeClass('d-none')
            } else {
                $("#support-department").addClass('d-none')
            }
        
            messageObj.onlineDepSupport.forEach(function (obj, index) {
                index++;
                $("#card-support").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });        
        
        //console.log(messageObj.usersOnline)
        $("#card-other").text('');
            if (usersOnlineIsEmpty > 0){
                $('#non-department').removeClass('d-none')
            } else {
                $("#non-department").addClass('d-none')
            }
            messageObj.usersOnline.forEach(function (obj, index) {
                index++;
                $("#card-other").append(renderUsersOnline(index, obj.uc_user_id, obj.username, obj.user_roles, obj.us_call_phone_status, obj.us_is_on_call));
            });               
        
        //console.log(messageObj.realtimeCalls)
        $("#card-live-calls").text('');
            messageObj.realtimeCalls.forEach(function (parent, index) {
                if(!parent.c_parent_id){
                    $("#card-live-calls").append(renderParentCalls(parent));
                    messageObj.realtimeCalls.forEach(function (child, childIndex) {
                        if (parent.c_id == child.c_parent_id){                            
                            $('#parent-' + parent.c_id).append(renderChildCalls(child));
                        }
                    });
                }                
            });
            
        //console.log(messageObj.callsHistory)
        $("#card-history-calls").text('');
            messageObj.callsHistory.forEach(function (parent, index) {
                if(!parent.c_parent_id){
                    $("#card-history-calls").append(renderParentCalls(parent));
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
    contentUpdate()
});

function contentUpdate() {
    $.ajax({
        url: '/call/realtime-user-map',
        type: 'POST',
        success: function(data) { 
            //console.info('Request data on connect');            
            $("#page-updated-time").text('').text(data.updatedTime); 
            startTimers();                
        }
    });
}

function renderParentCalls(parent)
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
                        parentCallProjectDetails(parent.project_name, parent.dep_name, parent.c_source_type_id) +            
                    '</td>' + 
                    '<td class="text-left">' +
                        showLeadCaseLinks(parent.c_lead_id, parent.gid, parent.c_case_id, parent.cs_gid) +      
                    '</td>' + 
                    '<td class="text-center">' + 
                        parentCallStatusDetails(parent.c_status_id, parent.c_created_dt, parent.c_updated_dt, parent.c_call_duration, parent.c_recording_sid) +    
                    '</td>' + 
                    '<td class="text-center" style="width:160px">' + 
                        callCreatedTime(parent.c_created_dt, parent.c_status_id, true, true) +
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
                        '<td class="text-left" style="width:80px">' +
                            showChildCallTimer(child.c_status_id, child.c_created_dt, child.c_updated_dt, child.c_call_duration, child.c_recording_sid) +
                        '</td>' +
                        '<td class="text-left">' +
                            renderChildCallUserAccesses(child.cua_status_ids, child.cua_user_ids, child.cua_user_names) +
                        '</td>' +
                        '<td class="text-center" style="width:90px">' +
                            callCreatedTime(child.c_created_dt, false, false, true) +
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

function parentCallProjectDetails(projectName, depName, callSourceTypeId) {
    let html = '';
    
    html+='<span class="badge badge-info">'+ projectName +'</span> <br>' +
          '<span class="label label-warning">'+ depName +'</span> '
        if(callSourceTypeId){
            html+='<span class="label label-info">'+ showShortSource(callSourceTypeId) +'</span>'  
        }          
    return html;
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
        html+= '<span class="label label-info">'+ showShortSource(callSourceTypeId) +'</span> ';
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

function callCreatedTime(callCreatedDate, cStatusID, enableSeconds, enableHours) {
    let html = '';
    let date = new Date(callCreatedDate);
    let timeStamp = date.getTime();
    let formattedTime = formatTime(new Date(timeStamp), enableSeconds, enableHours);
    let considerCallEnded = '$callIsEnded';    
    
    html+= '<i class="fa fa-clock-o"></i> '+ formattedTime +'<br>'
    
    if (Object.values(considerCallEnded).includes(cStatusID)){
        html+= calculateRelativeTime(callCreatedDate)
    }    
    
    return html;
}

function startTimers() {    
    $(".timer").each(function( index ) {
        var sec = $( this ).data('sec');
        var control = $( this ).data('control');
        var format = $( this ).data('format');            
        $(this).timer({format: format, seconds: sec}).timer(control);            
    });   
}

function formatTime(date, includeSeconds, includeHours) {
    let time = '';
    if (includeHours){
        time+= (date.getHours() < 10 ? '0' : '') + date.getHours() + ':';
    }
    
    time+=(date.getMinutes() < 10 ? '0' : '') + date.getMinutes();    
    
    if (includeSeconds){
        time+= ':' + (date.getSeconds() < 10 ? '0' : '') + date.getSeconds();
    }
    
    return time;
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

function showChildCallTimer(cStatusID, callCreatedDate, callUpdatedDate, callDuration, callSid) {
    let html = '';
    let sec = '';
    let considerCallEnded = '$callIsEnded';    
    let currentDate = Math.floor(new Date().getTime() / 1000);
    let createdDate = Math.floor(new Date(callCreatedDate).getTime() / 1000);
    let updatedDate = Math.floor(new Date(callUpdatedDate).getTime() / 1000);
    
    if (callUpdatedDate){
        if (Object.values(considerCallEnded).includes(cStatusID)){
            sec = callDuration > 0 ? callDuration : updatedDate - createdDate;                        
            html+= '<span class="badge badge-primary timer" data-sec="'+ sec +'" data-control="pause" data-format="%M:%S" style="font-size: 10px">'+ formatTime(new Date(sec * 1000), true, false) +'</span>';           
        } else {
            sec = currentDate - createdDate;                          
            html+='<span class="badge badge-warning timer" data-sec="'+ sec +'" data-control="start" data-format="%M:%S">'+ formatTime(new Date(sec * 1000), true, false) +'</span>';             
        }
    }    
    
    if(callSid){
        html+='<small><i class="fa fa-play-circle-o"></i></small>'
    }
    
    return html
}

function parentCallStatusDetails(cStatusID, callCreatedDate, callUpdatedDate, callDuration, callSid) {
    let html = '';
    let considerCallEnded = '$callIsEnded';
    let sec = 0;
    let currentDate = Math.floor(new Date().getTime() / 1000);
    let createdDate = Math.floor(new Date(callCreatedDate).getTime() / 1000);
    let updatedDate = Math.floor(new Date(callUpdatedDate).getTime() / 1000); 
    
    html+= showCurrentStatus(cStatusID);
     
    if (callUpdatedDate){       
        if (Object.values(considerCallEnded).includes(cStatusID)){
            sec = callDuration ? callDuration : updatedDate - createdDate;            
        } else {
            sec = currentDate - createdDate;            
        }
    }
    
    if (Object.values(considerCallEnded).includes(cStatusID)){
        html+= '<span class="badge badge-default">'+ formatTime(new Date(sec * 1000), true, false) +'</span>' 
        if(callSid){
            html+='<small><i class="fa fa-play-circle-o"></i></small>'
        }   
    } else {
        html+='<span class="badge badge-warning timer" data-sec="'+ sec +'" data-control="start" data-format="%M:%S">'+ formatTime(new Date(sec * 1000), true, false) +'</span>'              
    }
    
    return html;
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
            html+= '<i class="fa fa-user fa-2x fa-border"></i> ' + callCreatedUsername
        } else {
            html+= '<i class="fa fa-phone fa-2x fa-border"></i> ' + callFrom
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
    contentUpdate();
});

JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>

<style>
    #call-map-page table {margin-bottom: 5px}
</style>
<div id="call-map-page" class="col-md-12">
    <div class="row">
        <div class="col-md-2">
            <div id="sales-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SALES (<span id="card-sales-header-count"></span>)</div>
                <div id="card-sales" class="card-body">
                    <!-- User List: from SALES department -->
                </div>
            </div>

            <div id="exchange-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department EXCHANGE (<span id="card-exchange-header-count"></span>)</div>
                <div id="card-exchange" class="card-body">
                   <!-- User List: from EXCHANGE department -->
                </div>
            </div>

            <div id="support-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SUPPORT (<span id="card-support-header-count"></span>)</div>
                <div id="card-support" class="card-body">
                    <!-- User List: from SUPPORT department -->
                </div>
            </div>

            <div id="non-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <!--<div class="card-header"><i class="fa fa-users"></i> OnLine Users - W/O Department (1)</div>-->
                <div id="card-other" class="card-body">
                    <!-- User List: from W/O department -->
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"><i class="fa fa-list"></i> Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS (Updated: <i class="fa fa-clock-o"></i> <span id="page-updated-time"></span>)</div>
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
        <?=Html::button('Refresh Data', ['class' => 'btn btn-sm btn-success d-none', 'id' => 'btn-user-call-map-refresh'])?>
    </div>
</div>

