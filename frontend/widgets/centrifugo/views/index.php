<?php

use yii\bootstrap4\Html;

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 */

$passChannelsToJs = '["' . implode('", "', $channels) . '"]';

$js = <<<JS

var centrifuge = new Centrifuge('$centrifugoUrl');
centrifuge.setToken('$token');

let  channels = $passChannelsToJs;

channels.forEach(channelConnector)

function channelConnector(chName)
{
    centrifuge.subscribe(chName, function(message) {    
    let messageObj = JSON.parse(message.data.message);
    
    if(messageObj.count != undefined){        
        if(messageObj.count != 0) {            
            $(".cent-notification-counter").addClass('badge bg-green');
            $(".cent-notification-counter").text(messageObj.count);
        } else {
            $(".cent-notification-counter").removeClass('badge bg-green');
            $(".cent-notification-counter").text('');
        }
    } 
    
    if(messageObj.newMessages != undefined){
        let ids = getOldNotificationsIds()
                 
        $(".n-list").not(':last').remove();
       
        let msg = messageObj.newMessages        
        for (index = 0; index < msg.length; ++index) {                       
            let obj = msg[index];            
            $(".n-list:last").before(renderNotificationItems(obj.n_id, obj.n_title, obj.n_msg, obj.n_created_dt, obj.relative_created_dt)) 
        }
        
        for (index = 0; index < msg.length; ++index) {            
            let obj = msg[index];            
            if (!ids.includes(obj.n_id) && ids[0] !== undefined) {
                 console.log(ids)
                centNotify(obj.n_msg)
            }
        } 
    } 
});
}

centrifuge.connect();

centrifuge.on('connect', function(context) {
    // now client connected to Centrifugo and authorized
    centRefreshNotifications();
    console.info('Client connected to notifications server')
});

function getOldNotificationsIds()
{
    var ids = [];
    $( "#cent-notification-menu li").each(function(e) {
        let messageId = $(this).data('id');
        ids.push(messageId)        
    });   
    return ids;
}

function centNotify(message){
    createNotify('info', message, 'Centrifugo');
}

setInterval(centRefreshNotifications, 30 * 1000);
function centRefreshNotifications(){ 
    let counter = $(".cent-notification-counter").text();
    $.ajax({
            url: '/test/centrifugo-notifications',
            type: 'POST',
            data: {'msgCount' : counter != "" ? counter : 0 },            
            success: function(data) { 
                 //console.log('Trigger to centrifugo!!');                 
            }
     });
}

function renderNotificationItems(id, title, msg, createdDate, relativeTime) {
      return '<li data-id="'+ id +'" class="n-list">' +
          '<a href="javascript:;" onclick="notificationShow(this);" id="notification-menu-element-show" data-title="'+ title +'" data-id="'+ id +'">' +
              '<span class="glyphicon glyphicon-info-sign"> </span>' +
              '<span>' +
                  '<span> '+ title +'</span>' +
                  '<span class="time" data-time="'+ createdDate +'">'+ relativeTime +'</span>'+
              '</span>' +
              '<span class="message">'+ msg +'<br></span>' +
          '</a>'+
      '</li>';
}

JS;
$this->registerJs($js);
?>
<li class="dropdown open" role="presentation">
    <a href="javascript:;" onclick="notificationUpdateTime();" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown"
       aria-expanded="false">
        <i class="fa fa-comment-o"></i><span class="cent-notification-counter"> </span>
    </a>

    <ul id="cent-notification-menu" class="dropdown-menu list-unstyled msg_list" role="menu" x-placement="bottom-end">
        <!--<p> messages </p>-->
        <li class="n-list">
            <div class="text-center">
                <?= Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list']) ?>
            </div>
        </li>
    </ul>

</li>

