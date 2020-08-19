<?php
use sales\helpers\setting\SettingHelper;
/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 * @var $this yii\web\View
 */

$realtimeMonitorEnable = SettingHelper::isClientChatRealTimeMonitoringEnabled();

$passChannelsToJs ='["' . implode('", "', $channels) . '"]';

$js = <<<JS
let enableLiveUpdate = '$realtimeMonitorEnable'
let channels = $passChannelsToJs;
var centrifuge = new Centrifuge('$centrifugoUrl');
centrifuge.setToken('$token');

channels.forEach(channelConnector)

function channelConnector(chName)
{      
    centrifuge.subscribe(chName, function(message) {           
        let messageObj = JSON.parse(message.data.message);        
        if(messageObj.chatsData && messageObj.chatsData.length > 0){
            let ids = getRenderedChatIds()
            //console.log(messageObj.chatsData)
            messageObj.chatsData.forEach(function (chat, index) {
                if(!ids.includes(chat.cch_id)){
                    $("#card-live-chat").prepend(renderChat(chat));
                }                                              
            });
            getLastChatsUpdate()
            if (messageObj.chatsData.length > 0){               
                updateLocalTime()
                //updateMessagesRelativeTime()
            }           
        }
        
        if(messageObj.chatMessageData){
            let newMsg = messageObj.chatMessageData                           

            if(newMsg.client_id && !newMsg.user_id){ 
                renderNewClientMessage(newMsg.chat_id, newMsg.client_id, newMsg.msg, newMsg.sent_dt, newMsg.period)    
            }
            
            if (newMsg.client_id && newMsg.user_id){
                renderNewAgentMessage(newMsg.chat_id, newMsg.user_id, newMsg.msg, newMsg.sent_dt, newMsg.period)
            }             
            //updateMessagesRelativeTime()
            updateLocalTime()
        }
    });
}

centrifuge.connect();

centrifuge.on('connect', function(context) {        
    //console.info('Client connected to Centrifugo and authorized')
    contentUpdate("")
    updateLocalTime()        
});

function contentUpdate(chatsFromDateTime) {
    $.ajax({
        url: '/client-chat/monitor',
        type: 'POST',
        data: {"formDate": chatsFromDateTime},
        success: function(data) { 
            //console.info('Request data on connect'); 
        }
    });
}

function getLastChatsUpdate() {    
    if(enableLiveUpdate){
        setTimeout(function(){
         contentUpdate(getLastCreatedChatDate()) 
         getLastChatsUpdate(enableLiveUpdate) 
    }, 15000);
    }    
}

function getLastCreatedChatDate()
{    
    let dates = []; 
    let newestDate = '';
    $('.created-chat-time').each(function(i, obj) {
        dates.push($(this).text()) 
    })
    
    newestDate = dates[0];
    let newestDateObj = new Date(dates[0]);
    
    dates.forEach(function(date, index) {
        if (new Date(date) > newestDateObj){
            newestDate = date;
            newestDateObj = new Date(date)
        }
    }) 
    
    newestDate = new Date(newestDate)    
    newestDate.setSeconds( newestDate.getSeconds() + 1)   
    
    return formatDateTime(newestDate);   
}

function formatDateTime(date) {
    let dateTime = '';
        dateTime+= date.getFullYear() + '-';
        
        dateTime+= ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1) + '-';
        
        dateTime+= (date.getDate() < 10 ? '0' : '') + date.getDate() + ' ';
        
        dateTime+= (date.getHours() < 10 ? '0' : '') + date.getHours() + ':';
        
        dateTime+=(date.getMinutes() < 10 ? '0' : '') + date.getMinutes();    
    
        dateTime+= ':' + (date.getSeconds() < 10 ? '0' : '') + date.getSeconds();
   
    return dateTime;
}

function currentTime() {
    let d = new Date()
    let time = '';   
        time+= (d.getHours() < 10 ? '0' : '') + d.getHours() + ':';
       
        time+=(d.getMinutes() < 10 ? '0' : '') + d.getMinutes();    
    
        time+= ':' + (d.getSeconds() < 10 ? '0' : '') + d.getSeconds();   
    
    return time;
}

function updateLocalTime(){
    $("#page-updated-time").text(currentTime())
}

function renderChat(chat) {
    return '<div id="ch-'+ chat.cch_id +'" class="col-md-12">' +
                '<table class="table table-condensed table-client-chat-monitor">' +
                    '<tbody id="chat-'+ chat.cch_id +'">' +
                        '<tr class="warning">' +
                            '<td class="text-center" style="width:150px">' + 
                                renderGeneralInfo(chat.cch_id, chat.project, chat.cch_created_dt) +
                            '</td>' +
                            '<td class="text-center" style="width:190px">' +
                                renderAdditionalInfo(chat.department, chat.channel) +
                            '</td>' +
                            '<td class="text-left" style="width:250px">' +
                                renderAgentInfo(chat.cch_id, chat.cch_owner_user_id, chat.username, chat.outMsg, chat.email) +
                            '</td>' +                            
                            '<td id=m-cell-'+ chat.cch_id + '-'+ chat.cch_owner_user_id + ' class="text-left" style="width:450px">' +                        
                                renderAgentMessage(chat.cch_id, chat.cch_owner_user_id, chat.username, chat.agent_msg_date, chat.latest_agent_msg, chat.agent_msg_period) +
                            '</td>' +
                            '<td class="text-left" style="width:250px">' +
                                renderClientInfo(chat.cch_id, chat.cch_client_id, chat.clientName, chat.inMsg) +
                            '</td>' + 
                            '<td id=m-cell-'+ chat.cch_id + '-'+ chat.cch_client_id + ' class="text-left" style="width:450px">' +                            
                                renderClientMessage(chat.cch_id, chat.cch_client_id, chat.clientName, chat.client_msg_date, chat.latest_client_msg, chat.client_msg_period) +
                            '</td>' +
                        '</tr>' +
                    '</tbody>' +
                '</table>' +
           '</div>';
}

function renderGeneralInfo(id, projectName,  chatCreateDate) {
    let html = '';
       
    html+='<div class="media event d-flex justify-content-center">' +
                '<div class="media-body">' +
               /* '<a class="pull-left border-green profile_thumb">' +
                   '<i class="fa fa-comment green"></i>' +
               '</a>' + */
               '<div>' +
                    '<i class="fa fa-comment green"></i> <u><a class="chat-id-link" href="/client-chat-crud/view?id='+ id +'" target="_blank">'+ id +'</a></u><br>' +
                    /*'<span class="label label-danger" title="In messages">'+ inMsgCount +'</span> <br>' +
                    '<span class="label label-info" title="Out messages">'+ outMsgCount +'</span>' +*/
               '</div>' +
               '<div class="created-chat-time d-none">' +
                    chatCreateDate +
               '</div>';   
    if(projectName){
        html+='<span class="label label-info">'+ projectName +'</span>';
    }          
    html+='</div></div>';
    
    return html;
}

function renderAdditionalInfo(departmentName, channelName){
    let html = '';
    
    if(departmentName){
       html+= '<span class="label label-warning">'+ departmentName +'</span> ';
    }
    if(channelName){
      html+= '<span class="label label-info">'+ channelName +'</span>';
    } 
    
    return html;
}

function renderAgentMessage(chatID, agentID, agentName, msgDate, latestMsg, period){
    let html = '';
    
    if(!agentName){
       agentName = "...";
    }
    
    if(!agentID){
       agentID = "";
    }
    
    if(!period){
       period = "";
    }
    
    let msgLocator = chatID + '-' + agentID;    
        
    html+= '<div class="media event">' +                                  
                '<div class="media-body">' +                
                     '<p><i class="fa fa-clock"></i> '+ msgDate +' <strong id="time-'+ msgLocator +'" class="chat-relative-time first">'+ period +'</strong></p>' +
                     '<p><i id="icn-'+ msgLocator +'" class="fa fa-comment-o blue"></i> <small id="'+ msgLocator +'">'+ latestMsg +'</small>' +
                     '</p>' +
                '</div>' +
            '</div>';
    return html;                                    
}

function renderClientMessage(chatID, clientID, clientName, msgDate, latestMsg, period){
    let html = '';
    
    if(!clientName){
      clientName = "...";
    }
    
    if(!clientID){
      clientID = "";
    }
    
    if(!period){
       period = "";
    }
    
    let msgLocator = chatID + '-' + clientID;
    html+= '<div class="media event">' +                                  
                '<div class="media-body">' +                     
                      '<p><i class="fa fa-clock"></i> '+ msgDate +' <strong id="time-'+ msgLocator +'" class="chat-relative-time">'+ period +'</strong></p>' +
                      '<p><i id="icn-'+ msgLocator +'" class="fa fa-comment-o blue"></i> <small id="'+ msgLocator +'">'+ latestMsg +'</small>' +
                      '</p>' +
                '</div>' +
            '</div>';
    return html;
}

function renderNewClientMessage(chatID, clientID, msgBody, createdDt, period) {
    let msgLocator = chatID + '-' + clientID;    
    $('#' + msgLocator).text(msgBody);
    //$('#time-' + msgLocator).prop('title', createdDt);
    $('#time-' + msgLocator).text(period);
    $('#count-' + msgLocator).text(parseInt($('#count-' + msgLocator).text()) + 1)
    //$('#icn-' + msgLocator).addClass('blinking')
    //removeBlinking(msgLocator)
    
    setTimeout(function(){
        $('#m-cell-' + msgLocator).addClass('selected-cell')
    }, 1300)    
    
    removeCellBlinking(msgLocator)
    pushChatOnTop(chatID)
}

function renderNewAgentMessage(chatID, agentID, msgBody, createdDt, period) {
    let msgLocator = chatID + '-' + agentID;
    $('#' + msgLocator).text(msgBody);
    //$('#time-' + msgLocator).prop('title', createdDt);   
    $('#time-' + msgLocator).text(period);   
    $('#count-' + msgLocator).text(parseInt($('#count-' + msgLocator).text()) + 1)
    //$('#icn-' + msgLocator).addClass('blinking')
    //removeBlinking(msgLocator) 
    
    setTimeout(function(){
        $('#m-cell-' + msgLocator).addClass('selected-cell')
    }, 1300)    
       
    removeCellBlinking(msgLocator)
    pushChatOnTop(chatID)
}

function getRenderedChatIds() {
    let ids = []
    $('.chat-id-link').each(function(i, obj) {       
            ids.push($(this).text())               
    });
    return ids
}

/*function updateMessagesRelativeTime() {
    $('.chat-relative-time').each(function(i, obj) {
        if(obj.title){
            $(this).text(calculateRelativeTime(obj.title))
        }                
    }); 
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
}*/

function renderClientInfo(chatID, clientID, clientName, inMsgCount){
   let html = '';
   let countLocator = chatID + '-' + clientID; 
   if(!clientName){
      clientName = "...";
   }
    
    html+='<div class="media event">' +
              '<a class="pull-left border-blue profile_thumb">' +
                  '<i class="fa fa-user blue"></i>' +
              '</a>' +
              '<div class="media-body">' +
                    '<a class="title" href="/client/view?id='+ clientID +'" target="_blank">'+ clientName +'</a>' +
                    '<p><i class="fa fa-arrow-down red"></i> Sent Messages: <strong id="count-'+ countLocator +'">'+ inMsgCount +'</strong> </p>' +                    
              '</div>' +
          '</div>';
    return html; 
}

function renderAgentInfo(chatID, agentID,  agentName, outMsgCount, md5Email) {
   let html = '';
   let countLocator = chatID + '-' + agentID;
   if(!agentName){
      agentName = "...";
   }
   
   html+= '<div class="media event">' +
               '<img class="pull-left border-aero profile_thumb" src="//www.gravatar.com/avatar/'+ md5Email +'?d=identicon&amp;s=25" alt="">' +
               '<div class="media-body">' +
                     '<i class="fa fa-user-secret"></i> <a class="title" href="#">'+ agentName +'</a>' +
                         '<p><i class="fa fa-arrow-up green"></i> Sent Messages: <strong id="count-'+ countLocator +'">'+ outMsgCount +'</strong> </p>' +                         
               '</div>' +
          '</div>'
   return html
}

/*function removeBlinking(id) {
  setTimeout(function(){
      $('#icn-' + id).removeClass('blinking');                        
  }, 7000);
}*/

function removeCellBlinking(id) {
  setTimeout(function(){
      $('#m-cell-' + id).removeClass('selected-cell');                        
  }, 2000);
}

function pushChatOnTop(chatID) {    
   let parentElement = document.getElementById('card-live-chat')
   let childElement = document.getElementById('ch-' + chatID)
   let topChatId = document.getElementById("card-live-chat").firstChild.id
    
   if (chatID != topChatId.split("-")[1]){       
       $("#ch-" + chatID).hide('slow', function() {
            parentElement.insertBefore(childElement, parentElement.firstChild)
       }); 
              
       $("#ch-" + chatID).show('slow');       
    }   
}
    
JS;
$this->registerJs($js, \yii\web\View::POS_READY);

?>

<div id="client-chat-page" class="col-md-12">
    <div class="card card-default">
        <div class="card-header"><i class="fa fa-list"></i> CLIENT CHAT REAL-TIME MONITORING (Updated: <i class="fa fa-clock-o"></i> <span id="page-updated-time">00:00:00</span>)</div>
            <table class="table table-condensed table-client-chat-monitor jambo_table">
                <thead>
                <tr>
                    <th class="column-title text-center chat-monitor-th" style="display: table-cell; width:160px;">Chat ID / Project </th>
                    <th class="column-title text-center chat-monitor-th" style="display: table-cell; width:190px;">Department / Channel </th>
                    <th class="column-title chat-monitor-th" style="display: table-cell; width:250px;">Agent Info </th>
                    <th class="column-title chat-monitor-th" style="display: table-cell; width:450px;">Agent Last Message </th>
                    <th class="column-title chat-monitor-th" style="display: table-cell; width:250px;">Client info </th>
                    <th class="column-title chat-monitor-th" style="display: table-cell; width:450px;">Client Last Message </th>
                </tr>
                </thead>
            </table>

        <div id="card-live-chat" class="card-body card-monitor">
            <!-- real-time content -->
        </div>
    </div>
</div>
