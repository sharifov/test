<?php
use yii\bootstrap4\Html;
/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 */

$passChannelsToJs ='["' . implode('", "', $channels) . '"]';

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
    
    if(messageObj.msg != undefined){
        console.log(messageObj);
        let obj = messageObj.msg;
        centNotify(obj.n_msg)
               
        //$("#cent-notification-menu").prepend(renderNotificationItems(obj.n_id, obj.n_title, obj.n_msg, obj.n_created_dt, obj.relative_created_dt))
        $(".n-list:last").before(renderNotificationItems(obj.n_id, obj.n_title, obj.n_msg, obj.n_created_dt, obj.relative_created_dt))
    } 
    
});
}

centrifuge.connect();

centrifuge.on('connect', function(context) {
    // now client connected to Centrifugo and authorized
    centRefreshNotifications();
    console.info('Client connected to notifications server')
});

function centNotify(message){
    new PNotify({
        type: 'info',
        title: 'Centrifugo',
        text: message,
        icon: true,
        /*desktop: {
            desktop: true,
            fallback: true,
            text: message.data.message
        },*/
        delay: 10000,
        mouse_reset: false,
        hide: true
    });
}

//setInterval(centRefreshNotifications, 30 * 1000);
function centRefreshNotifications(){
    $.ajax({
            url: '/test/centrifugo-notifications',
            type: 'POST',            
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
        <!--<p>Test message</p>-->
        <li class="n-list">
            <div class="text-center">
                <?= Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list']) ?>
            </div>
        </li>
    </ul>

</li>

