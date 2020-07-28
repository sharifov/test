<?php

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 * @var $this yii\web\View
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
        if(messageObj.chatsData){
            console.log(messageObj.chatsData)
            $("#card-live-chat").text('');
            messageObj.chatsData.forEach(function (chat, index) {
                $("#card-live-chat").append(renderChat(chat));                              
            });
        }
        
        if(messageObj.chatMessageData){
            let newMsg = messageObj.chatMessageData
            
            if(newMsg.client_id && !newMsg.user_id){
                console.log(newMsg)
                renderNewClientMessage(newMsg.chat_id, newMsg.client_id, newMsg.msg, newMsg.sent_dt)                
            }
            
            if (newMsg.client_id && newMsg.user_id){
                console.log("Agent Message")
                console.log(newMsg)
                renderNewAgentMessage(newMsg.chat_id, newMsg.user_id, newMsg.msg, newMsg.sent_dt)
            }             
            updateMessagesRelativeTime()
        }
    });
}

centrifuge.connect();

centrifuge.on('connect', function(context) {        
    //console.info('Client connected to Centrifugo and authorized')
    contentUpdate()    
});

function contentUpdate() {
    $.ajax({
        url: '/client-chat/monitor',
        type: 'POST',
        success: function(data) { 
            //console.info('Request data on connect');            
            //$("#page-updated-time").text('').text(data.updatedTime); 
            //startTimers();                
        }
    });
}

function renderChat(chat) {
    return '<div id="ch-'+ chat.cch_id +'" class="col-md-12" style="margin-bottom:2px">' +
                '<table class="table table-condensed table-client-chat-monitor">' +
                    '<tbody id="chat-'+ chat.cch_id +'">' +
                    '<tr class="warning">' +
                        '<td class="text-center" style="width:150px">' + 
                            renderGeneralInfo(chat.cch_id) +
                        '</td>' +
                        '<td class="text-left" style="width:250px">' +
                            renderAgentInfo(chat.cch_id, chat.cch_owner_user_id, chat.username, chat.outMsg) +
                        '</td>' +
                        '<td class="text-left" style="width:250px">' +
                            renderClientInfo(chat.cch_id, chat.cch_client_id, chat.clientName, chat.inMsg) +
                        '</td>' +
                        '<td class="text-center" style="width:130px">' +
                            renderProjectInfo(chat.project, chat.department, chat.channel) +
                        '</td>' +
                        
                        '<td class="text-left" style="width:450px">' +                        
                            renderAgentMessage(chat.cch_id, chat.cch_owner_user_id, chat.username) +
                        '</td>' +
                        '<td class="text-left" style="width:450px">' +                            
                            renderClientMessage(chat.cch_id, chat.cch_client_id, chat.clientName) +
                        '</td>' +
                        
                        /*'<td class="text-left"><i>l:<a href="/lead/view/0fb7b8b6cb49f458be2cc5fb5b4f4aa1" target="_blank">503669</a></i><br></td>' +*/
                        /*'<td class="text-center"><i class="fa fa-pause text-success"></i> Delay<br><span class="badge badge-warning timer" data-sec="10965160" data-control="start" data-format="%M:%S">59:50</span></td>' +*/
                        /*'<td class="text-center" style="width:160px"><i class="fa fa-clock-o"></i> 11:26:49<br></td>' +*/
                        /*'<td class="text-left" style="width:160px"><i class="fa fa-fax fa-1x fa-border"></i> +18552068194<i class="fa fa-user fa-1x fa-border"></i> Linda</td>' +*/
                    '</tr>' +
                    '</tbody>' +
                '</table>' +
            '</div>';
}


function renderAgentMessage(chatID, agentID, agentName){
    let html = '';
    
    if(!agentName){
      agentName = "...";
    }
    
    if(!agentID){
      agentID = "";
    }
    
    let msgLocator = chatID + '-' + agentID;    
        
    html+= '<div class="media event">' +                                  
                '<div class="media-body">' +
                /*'<a class="title" href="#">test </a>' +*/
                     '<p><i class="fa fa-comment-o red"></i> last send by '+ agentName +' <strong title="" id="time-'+ msgLocator +'" class="chat-relative-time first"> </strong></p>' +
                     '<p> <small id="'+ msgLocator +'">No new message from agent ...</small>' +
                     '</p>' +
                '</div>' +
            '</div>';
    return html;                                    
}

function renderClientMessage(chatID, clientID, clientName){
    let html = '';
    let msgLocator = chatID + '-' + clientID;
    html+= '<div class="media event">' +                                  
                '<div class="media-body">' +
                     /* '<a class="title" href="#">test </a>' +*/
                      '<p><i class="fa fa-comment-o red"></i> last send by '+ clientName +' <strong title="" id="time-'+ msgLocator +'" class="chat-relative-time"> </strong></p>' +
                      '<p> <small id="'+ msgLocator +'">No new message from client ...</small>' +
                      '</p>' +
                '</div>' +
            '</div>';
    return html;
}

function renderNewClientMessage(chatID, clientID, msgBody, createdDt) {
    let msgLocator = chatID + '-' + clientID;
    $('#' + msgLocator).text(msgBody);
    $('#time-' + msgLocator).prop('title', createdDt);
    $('#count-' + msgLocator).text(parseInt($('#count-' + msgLocator).text()) + 1)
    $('#icn-' + msgLocator).addClass('icon-pulse')
    removePulse()
}

function renderNewAgentMessage(chatID, agentID, msgBody, createdDt) {
    let msgLocator = chatID + '-' + agentID;
    $('#' + msgLocator).text(msgBody);
    $('#time-' + msgLocator).prop('title', createdDt);   
    $('#count-' + msgLocator).text(parseInt($('#count-' + msgLocator).text()) + 1)
    $('#icn-' + msgLocator).addClass('icon-pulse')
    removePulse()
}

function updateMessagesRelativeTime() {
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
}

function renderProjectInfo(projectName, departmentName, channelName){
    let html = '';
    if(!projectName){
      projectName = "...";
    }
    if(!departmentName){
      departmentName = "...";
    }
    if(!channelName){
      channelName = "...";
    }
    html+= '<span class="badge badge-info">'+ projectName +'</span> <br>' +
           '<span class="label label-warning">'+ departmentName +'</span> ' +
           '<span class="label label-info">'+ channelName +'</span>';
    
    return html;
}

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
                    '<p><i id="icn-'+ countLocator +'" class="fa fa-arrow-down red"></i> Sent Messages: <strong id="count-'+ countLocator +'">'+ inMsgCount +'</strong> </p>' +
                    /*'<p> <small>12 Sales Today</small>' +*/
                    '</p>' +
              '</div>' +
          '</div>';
    return html; 
}

function renderAgentInfo(chatID, agentID,  agentName, outMsgCount) {
   let html = '';
   let countLocator = chatID + '-' + agentID;
   if(!agentName){
      agentName = "...";
   }
   
   html+= '<div class="media event">' +
               '<a class="pull-left border-aero profile_thumb">' +
                       '<i class="fa fa-user-secret aero"></i>' +
               '</a>' +
               '<div class="media-body">' +
                     '<a class="title" href="#">'+ agentName +'</a>' +
                         '<p><i id="icn-'+ countLocator +'" class="fa fa-arrow-up green"></i> Sent Messages: <strong id="count-'+ countLocator +'">'+ outMsgCount +'</strong> </p>' +
                         /*'<p> <small>12 Sales Today</small></p>' +*/
               '</div>' +
          '</div>'
   return html
}

function renderGeneralInfo(id) {
    let html = '';
    html+='<div class="media event d-flex justify-content-center">' +
               /*'<a class="pull-left border-green profile_thumb">' +
                   '<i class="fa fa-comment green"></i>' +
               '</a>' +*/
               '<div class="">' +
                    '<i class="fa fa-comment green"></i> <u><a href="/client-chat-crud/view?id='+ id +'" target="_blank">'+ id +'</a></u><br>' +
                    /*'<span class="label label-danger" title="In messages">'+ inMsgCount +'</span> <br>' +
                    '<span class="label label-info" title="Out messages">'+ outMsgCount +'</span>' +*/
               '</div>' +
          '</div>';
    return html;
}

function removePulse() {
  setTimeout(function(){
            $("i").removeClass('icon-pulse');
            //....and whatever else you need to do
            pushChatOnTop()
    }, 10000);
}

function pushChatOnTop() {    
  let parentElement = document.getElementById('card-live-chat')
  let childElement = document.getElementById('ch-6')  
 
  $("#ch-6").hide('slow', function() {
    parentElement.insertBefore(childElement, parentElement.firstChild)
  }); 
  
  $("#ch-6").show('slow');
}
    
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

?>

<div id="client-chat-page" class="col-md-12">
    <div class="card card-default">
        <div class="card-header"><i class="fa fa-list"></i> CLIENT CHAT REAL-TIME MONITORING (Updated: <i class="fa fa-clock-o"></i> <span id="page-updated-time">10:19:29</span>)</div>
        <div id="card-live-chat" class="card-body">
            <!-- real-time content -->
        </div>
    </div>
</div>
