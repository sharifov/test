<?php
use sales\auth\Auth;
use frontend\widgets\centrifugo\RealtimeCallUserMapWidget;

?>

<?= RealtimeCallUserMapWidget::widget([
    'userId' => Auth::id(),
    'userAllowedChannels' => [
        'realtimeUserMapChannel#' . Auth::id(),
    ]
]) ?>
