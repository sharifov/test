<?php
use sales\auth\Auth;
use frontend\widgets\centrifugo\RealtimeClientChatMonitorWidget;

?>

<?= RealtimeClientChatMonitorWidget::widget([
    'userId' => Auth::id(),
    'userAllowedChannels' => [
        'realtimeClientChatChannel#' . Auth::id(),
    ]
]) ?>