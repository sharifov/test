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

?>

<div class="row room_box justify-content-center">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <h5><?= Html::encode($this->title) ?></h5>
        <div id="_rc-iframe-wrapper" style="height: 95%; width: 100%; position: relative; min-height: 740px;">
            <?php if ($clientChat): ?>
                <?php echo $this->render('../client-chat/partial/_chat_history', ['clientChat' => $clientChat]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>
