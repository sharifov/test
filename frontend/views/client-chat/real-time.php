<?php
/* @var $this yii\web\View */

use yii\bootstrap4\Html;

$this->title = 'Client Chat - Real Time';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-real-time">
    <h1><?= Html::encode($this->title) ?></h1>
    <div style="width: 100%" id="client-chat-realtime-div"></div>
</div>

<?php
$js = <<<JS
(function(){function b(){var a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src="https://cdn.travelinsides.com/npmstatic/chatapi-dev.min.js";document.getElementsByTagName("head")[0].appendChild(a)}window.k=window.k||{};window.k.livechat=window.k.livechat||{};var c=[];["create","setCustomProps","track","onReady"].forEach(function(a){window.k.livechat[a]=function(){c.push([a,arguments])}});window.k.livechat.queue=c;"complete"===document.readyState?
b():window.addEventListener("load",b)})();

  var run = function () {
    window.k.realtimeVisitors(document.getElementById('client-chat-realtime-div'), {
      host: 'https://dev-livechat.travel-dev.com/visitors'
    });
  }

  var t = setInterval(function() {
    if (window.k) {
      clearInterval(t);
      run();
    }
  }, 50);

JS;
$this->registerJs($js, \yii\web\View::POS_READY);

