<?php
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
    console.log(message);
    
    new PNotify({
        type: 'info',
        title: 'Centrifugo',
        text: message.data.message,
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
});
}


centrifuge.connect();

JS;
$this->registerJs($js);
