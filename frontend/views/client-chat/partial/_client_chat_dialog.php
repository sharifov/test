<?php

/**
 * @var string $agentToken
 * @var string $server
 * @var string $apiServer
 * @var string $chatApiScriptUrl
 * @var string $rid
 * @var int $readonly
 * @var \yii\web\View $this
 */

use yii\helpers\Url;

$refreshAgentToken = Url::to('/client-chat/ajax-refresh-user-chat-token');
?>

<div id="manual-refresh-token"><button id="manual-refresh-token-btn" class="btn btn-success btn-sm"><i class="fa fa-sync"></i> Refresh auth token</button></div>
<div id="refresh-token-processing">Wait while the token is updated <i class="fa fa-spinner fa-spin" style="margin-left: 7px"></i></div>
<div id="chat-dialog"></div>

<?php

$js = <<<JS
window.chatAgentToken = '$agentToken';

(function(){function b(){
    var a=document.createElement("script");
    a.type="text/javascript";a.async=!0;
    a.src="$chatApiScriptUrl";
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

window.refreshRcAgentToken = function () {
    $('#refresh-token-processing').addClass('active');
    $.post('$refreshAgentToken', {}, (data) => {
        if (data.error) {
            createNotify('Warning', data.message, 'warning');
            $('#manual-refresh-token').addClass('active');
        } else {
            $('#manual-refresh-token').removeClass('active');
        }
        
        $('#manual-refresh-token-btn').find('.fa').removeClass('fa-spin');
        $('#manual-refresh-token-btn').prop('disabled', false).removeClass('disabled');
    }, 'json')
    .fail( function (xhr) {
        createNotify('Error', xhr.responseText, 'error');
        $('#manual-refresh-token').addClass('active');
        $('#manual-refresh-token-btn').find('.fa').removeClass('fa-spin');
        $('#manual-refresh-token-btn').prop('disabled', false).removeClass('disabled');
    })
    .always( () => {
        $('#refresh-token-processing').removeClass('active');
    });
}

window.initChatDialog = function (params) {
  let chatDialogContainer = document.getElementById('chat-dialog');
  chatDialogContainer.classList.add('active');
  
  params.onError = function (error) {
      window.refreshRcAgentToken();
  };
  
  if (typeof window.chatDialog === 'function') {
    window.chatDialog(params);
  } else {
    window.chatDialog = window.k.crmChat(chatDialogContainer, params);
  }
  
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);

$js = <<<JS
$(document).on('click', '#manual-refresh-token-btn', function () {
    let html = $(this).html();
    $(this).find('.fa').addClass('fa-spin');
    $(this).prop('disabled', true).addClass('disabled');
    window.refreshRcAgentToken();
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
if ($rid) {
    $js = <<<JS
  var t = setInterval(function () {
    if (window.k && window.k.crmChat) {
      clearInterval(t);
      initChatDialog({
        token: window.chatAgentToken,
        server: '$server',
        rid: '$rid',
        readonly: Boolean($readonly),
        apiServer: '$apiServer'
      });
    }
  }, 50);
JS;

    $this->registerJs($js, \yii\web\View::POS_LOAD);
}
