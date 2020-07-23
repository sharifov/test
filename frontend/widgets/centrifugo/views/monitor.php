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
        console.log(messageObj.chatsData)
        $("#card-live-chat").text('');
            messageObj.chatsData.forEach(function (chat, index) {
                $("#card-live-chat").append(renderChat(chat));
                /*if(!chat.c_parent_id){
                    $("#card-live-calls").append(renderParentCalls(chat));
                    messageObj.realtimeCalls.forEach(function (child, childIndex) {
                        if (chat.c_id == child.c_parent_id){                            
                            $('#parent-' + chat.c_id).append(renderChildCalls(child));
                        }
                    });
                }*/                
            });
    });
}

centrifuge.connect();

centrifuge.on('connect', function(context) {        
    console.info('Client connected to Centrifugo and authorized')
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
    return '<div class="col-md-12" style="margin-bottom:2px">' +
                '<table class="table table-condensed table-client-chat-monitor">' +
                    '<tbody id="chat-'+ chat.cch_id +'">' +
                    '<tr class="warning">' +
                        '<td class="text-center" style="width:150px">' + 
                            renderGeneralInfo(chat.cch_id, chat.inMsg, chat.outMsg) +
                        '</td>' +
                        '<td class="text-left" style="width:250px">' +
                             renderAgentInfo(chat.username) +
                        '</td>' +
                        '<td class="text-left" style="width:250px">' +
                            renderClientInfo(chat.clientName) +
                        '</td>' +
                        '<td class="text-center" style="width:130px">' +
                            renderProjectInfo(chat.project, chat.department, chat.channel) +
                        '</td>' +
                        
                        '<td class="text-left" style="width:450px">' +
                        
                            '<div class="media event">' +
                                  
                                  '<div class="media-body">' +
                                        /*'<a class="title" href="#">test </a>' +*/
                                       /* '<p><i class="fa fa-comment-o red"></i> <strong>$2300. </strong> Client Payed </p>' +*/
                                       '<p><i class="fa fa-comment-o red"></i> ' +
                                            '<span>by John Smith</span>' +
                                            '<span class="time">3 mins ago</span>' +
                                        '</p>' +
                                        '<p> <small>Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay…</small>' +
                                        '</p>' +
                                  '</div>' +
                              '</div>'+
                        '</td>' +
                        '<td class="text-left" style="width:450px">' +
                            
                            '<div class="media event">' +
                                  
                                  '<div class="media-body">' +
                                       /* '<a class="title" href="#">test </a>' +*/
                                        '<p><i class="fa fa-comment-o red"></i> <strong>$2300. </strong> Client Payed </p>' +
                                        '<p> <small>Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay…' +
                                        '</p>' +
                                  '</div>' +
                              '</div>'+
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

function renderClientInfo(clientName){
   let html = '';
    
   if(!clientName){
      clientName = "...";
   }
    
    html+='<div class="media event">' +
              '<a class="pull-left border-blue profile_thumb">' +
                  '<i class="fa fa-user blue"></i>' +
              '</a>' +
              '<div class="media-body">' +
                    '<a class="title" href="#">'+clientName +' </a>' +
                    '<p><strong>$2300. </strong> Client Payed </p>' +
                    '<p> <small>12 Sales Today</small>' +
                    '</p>' +
              '</div>' +
          '</div>';
    return html; 
}

function renderAgentInfo(agentName) {
   let html = '';
   
   if(!agentName){
      agentName = "...";
   }
   
   html+= '<div class="media event">' +
               '<a class="pull-left border-aero profile_thumb">' +
                       '<i class="fa fa-user-secret aero"></i>' +
               '</a>' +
               '<div class="media-body">' +
                     '<a class="title" href="#">'+ agentName +'</a>' +
                         '<p><strong>$2300. </strong> Agent Avarage Sales </p>' +
                         '<p> <small>12 Sales Today</small></p>' +
               '</div>' +
          '</div>'
   return html
}

function renderGeneralInfo(id, inMsgCount, outMsgCount) {
    let html = '';
    html+='<div class="media event d-flex justify-content-center">' +
               '<a class="pull-left border-green profile_thumb">' +
                   '<i class="fa fa-comment green"></i>' +
               '</a>' +
               '<div class="">' +
                    '<u><a href="/call/view?id=3260694" target="_blank">'+ id +'</a></u><br>' +
                    '<span class="label label-danger" title="In messages">'+ inMsgCount +'</span> <br>' +
                    '<span class="label label-info" title="Out messages">'+ outMsgCount +'</span>' +
               '</div>' +
          '</div>';
    return html;
}
    
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

?>

<div id="client-chat-page" class="col-md-12">

    <div class="card card-default">
        <div class="card-header"><i class="fa fa-list"></i> CLIENT CHAT REAL-TIME MONITORING (Updated: <i class="fa fa-clock-o"></i> <span id="page-updated-time">10:19:29</span>)</div>
        <div id="card-live-chat" class="card-body">


        </div>
    </div>
</div>
