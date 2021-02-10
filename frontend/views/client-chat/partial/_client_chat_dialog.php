<?php
/**
 * @var string $agentToken
 * @var string $server
 * @var string $rid
 * @var int $readonly
 * @var \yii\web\View $this
 */


?>

<div id="chat-dialog">
</div>

<?php
$js = <<<JS
var t = setInterval(function() {
   if (window.k && window.k.crmChat) {
      clearInterval(t);
      initChatDialog('$agentToken', '$server', '$rid', Boolean($readonly));
    }
  }, 50);
JS;

$this->registerJs($js);
