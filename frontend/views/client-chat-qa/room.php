<?php

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var ClientChat $clientChat */
/* @var ClientChatMessage|null $history */

$this->title = 'Client Chat Room: ' . $clientChat->cch_rid;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats QA', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Room';

$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';

$rcUrl = Yii::$app->rchat->host  . '/live/' . urlencode($clientChat->cch_rid) . '?layout=embedded&readonly';
?>

<h5><?= Html::encode($this->title) ?></h5>

<div class="row room_box">
    <div class="col-md-6">
        <div id="_rc-iframe-wrapper" style="height: 100%; width: 100%; position: relative; min-height: 740px;">
            <?php if ($clientChat && !$clientChat->isClosed()): ?>
                <iframe
                    class="_rc-iframe"
                    src="<?= $rcUrl ?>"
                    id="_rc-<?= $clientChat->cch_id ?>"
                    style="border: none; width: 100%; height: 100%;" ></iframe>
            <?php elseif ($clientChat && $clientChat->isClosed()): ?>
                <?= $this->render('../client-chat/partial/_chat_history', ['history' => $history, 'clientChat' => $clientChat]) ?>
            <?php endif; ?>
        </div>
    </div>

</div>
