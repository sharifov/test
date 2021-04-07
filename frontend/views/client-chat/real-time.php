<?php

/* @var $this yii\web\View */
/* @var $host string */
/* @var $projectsWithKeys string */

use yii\bootstrap4\Html;

$this->title = 'Client Chat - Real Time Visitors';
$this->params['breadcrumbs'][] = $this->title;

$chatApiScriptUrl = Yii::$app->rchat->chatApiScriptUrl;
?>
<div class="client-chat-real-time">
    <h3><i class="fa fa-comments"></i> <?= Html::encode($this->title) ?></h3>
    <div style="width: 100%; height: 800px" id="client-chat-realtime-div"></div>
</div>

<?php
$url = \yii\helpers\Url::toRoute('/client-chat/real-time-start-chat');
$js = <<<JS
(function(){function b(){var a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src="$chatApiScriptUrl";document.getElementsByTagName("head")[0].appendChild(a)}window.k=window.k||{};window.k.livechat=window.k.livechat||{};var c=[];["create","setCustomProps","track","onReady"].forEach(function(a){window.k.livechat[a]=function(){c.push([a,arguments])}});window.k.livechat.queue=c;"complete"===document.readyState?
b():window.addEventListener("load",b)})();

    
  var run = function () {
    window.k.realtimeVisitors(document.getElementById('client-chat-realtime-div'), {
      host: '$host',
      settings: {
        writeMessageEnabled: true,
        project: JSON.parse('$projectsWithKeys')
      }
    }).then( function (instance) {
        instance.events.on('chat-created', function (e) {
            let visitorId = e.visitorId;
            let projectName = e.project;
            let visitorName = e.visitor.name;
            let visitorEmail = e.visitor.email;
            
            var modal = $('#modal-sm');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> </div>');
            modal.modal('show').find('.modal-header').html('<h3>Send Message ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$url', {visitorId: visitorId, projectName: projectName, visitorName: visitorName, visitorEmail: visitorEmail}, function(data) {
                modal.find('.modal-body').html(data);
            }).fail( function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
                setTimeout(function () {
                    $("#modal-sm").modal("hide");
                }, 500);
            });
                
           return false;
        });
    }).catch(function () {
        console.error('Chat created event error has occurred');
    });
  }

  var t = setInterval(function() {
   if (window.k && window.k.realtimeVisitors) {
      clearInterval(t);
      run();
    }
  }, 50);

JS;
$this->registerJs($js, \yii\web\View::POS_READY);

