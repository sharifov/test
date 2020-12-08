<?php

use sales\auth\Auth;
use frontend\widgets\centrifugo\CentrifugoWidget;

?>

<?= CentrifugoWidget::widget([
    'userId' => Auth::id(),
    'widgetView' => 'connect-user-map',
    'userAllowedChannels' => [
        'realtimeUserMapChannel#' . Auth::id(),
    ]
]);
