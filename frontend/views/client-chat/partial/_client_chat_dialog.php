<?php
/**
 * @var string $agentToken
 * @var string $server
 * @var string $apiServer
 * @var string $rid
 * @var int $readonly
 * @var \yii\web\View $this
 */


?>

<div id="chat-dialog"></div>

<?php

$js = <<<JS
(function(){function b(){
    var a=document.createElement("script");
    a.type="text/javascript";a.async=!0;
    a.src="https://cdn.travelinsides.com/npmstatic/chatapi-dev.min.js";
    document.getElementsByTagName("head")[0].appendChild(a)
}
window.k=window.k||{};
window.k.livechat=window.k.livechat||{};
var c=[];["create","setCustomProps","track","onReady"].forEach(function(a){
    window.k.livechat[a]=function(){c.push([a,arguments])}
});
window.k.livechat.queue=c;
b();
// "complete"===document.readyState?b():window.addEventListener("load",b)
})();

window.initChatDialog = function (params) {
  let chatDialogContainer = document.getElementById('chat-dialog');
  chatDialogContainer.classList.add('active');
  
  console.log(params);
  if (typeof window.chatDialog === 'function') {
    window.chatDialog(params);
  } else {
    window.chatDialog = window.k.crmChat(chatDialogContainer, params);
  }
  
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
if ($rid) {
    $js = <<<JS
var t = setInterval(function() {
   if (window.k && window.k.crmChat) {
      clearInterval(t);
      initChatDialog({
        token: '$agentToken',
        server: '$server',
        rid: '$rid',
        readonly: Boolean($readonly),
        apiServer: '$apiServer'
      });
    }
  }, 50);
JS;

    $this->registerJs($js, \yii\web\View::POS_HEAD);
}
