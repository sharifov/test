<?php

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $userId int */
/* @var $userIdentity string */
/* @var $wsUrl string */

\frontend\assets\WebSocketAsset::register($this);
?>
<li>
    <a href="javascript:;" class="info-number" title="Online Connection" id="online-connection-indicator">
        <i class="fa fa-plug"></i>
    </a>
</li>

<?php
$ccNotificationUpdateUrl = Url::to(['/client-chat/refresh-notification']);
$discardUnreadMessageUrl = Url::to(['/client-chat/discard-unread-messages']);

$js = <<<JS
   
    window.socket = null;
    window.socketConnectionId = null;
    window.userIdentity = '$userIdentity';
    
    let userId = '$userId';
    let wsUrl = '$wsUrl';
    let ccNotificationUpdateUrl = '$ccNotificationUpdateUrl';
    let discardUnreadMessageUrl = '$discardUnreadMessageUrl';

    wsInitConnect(wsUrl, 10000, userId, $('#online-connection-indicator'), ccNotificationUpdateUrl, discardUnreadMessageUrl);
JS;
$this->registerJs($js, \yii\web\View::POS_READY, 'ws-connection-js');
