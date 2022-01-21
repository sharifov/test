<?php

use src\auth\Auth;
use frontend\widgets\centrifugo\CentrifugoWidget;

?>

<?= CentrifugoWidget::widget([
    'userId' => Auth::id(),
    'widgetView' => 'monitor',
    'userAllowedChannels' => [
        'realtimeClientChatChannel',
    ]
]);
