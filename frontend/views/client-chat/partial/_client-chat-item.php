<?php

use common\models\Email;
use sales\helpers\clientChat\ClientChatHelper;
use sales\helpers\clientChat\ClientChatMessageHelper;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $clientChats ClientChat[] */
/** @var $clientChatId int|null */
?>

<?php foreach($clientChats as $clientChat): ?>
    <?php
        $lastChatMessage = $lastChatMessageDate = '';
        $lastClientMessage = $clientChat->getLastMessageByClient();
        $lastAgentMessage = $clientChat->getLastMessageByAgent();

        if($lastClientMessage && !$lastAgentMessage){
            $lastChatMessage = $lastClientMessage->message;
            $lastChatMessageDate = $lastClientMessage->ccm_sent_dt;
        } elseif ($lastAgentMessage && !$lastClientMessage){
            $lastChatMessage = $lastAgentMessage->message;
            $lastChatMessageDate = $lastAgentMessage->ccm_sent_dt;
        } elseif ($lastClientMessage && $lastAgentMessage) {
            if(strtotime($lastClientMessage->ccm_sent_dt) > strtotime($lastAgentMessage->ccm_sent_dt)){
                $lastChatMessage = $lastClientMessage->message;
                $lastChatMessageDate = $lastClientMessage->ccm_sent_dt;
            }
            else{
                $lastChatMessage = $lastAgentMessage->message;
                $lastChatMessageDate = $lastAgentMessage->ccm_sent_dt;
            }
        }
    ?>

    <div class="_cc-list-item <?= $clientChatId && $clientChatId === $clientChat->cch_id ? '_cc_active' : '' ?> <?= $clientChat->getStatusClass() ?> " data-goto-param="/live/<?= $clientChat->cch_rid ?>?layout=embedded" data-rid="<?= $clientChat->cch_rid ?>" data-cch-id="<?= $clientChat->cch_id ?>" data-is-closed="<?= $clientChat->isClosed() ?>">
        <div class="_cc-item-icon-wrapper">
            <span class="_cc-item-icon-round">
                <i class="fa fa-comment"></i>
                <span class="_cc-status-wrapper">
                    <span class="_cc-status" data-is-online="<?= (int)$clientChat->cch_client_online ?>"></span>
                </span>
                <?php $unreadMessages = ClientChatMessageHelper::getCountOfChatUnreadMessage($clientChat->cch_id, $clientChat->cch_owner_user_id) ?>
                    <span class="_cc-chat-unread-message">
                        <span class="badge badge-info _cc_item_unread_messages" data-cch-id="<?= $clientChat->cch_id ?>"><?php if ($unreadMessages): ?><?= $unreadMessages ?><?php endif; ?></span>
                    </span>
            </span>
            <span class="_cc-title">
                <p><b><?= Html::encode(ClientChatHelper::getClientName($clientChat)) ?></b></p>
                <p title="Сhat creation date"><small><?= Yii::$app->formatter->asDate($clientChat->cch_created_dt,'php:Y-M-d') ?></small></p>
                <?php \yii\widgets\Pjax::begin(['id' => 'chat-last-message-refresh']) ?>
                    <?php if ($lastChatMessage) : ?>
                        <p><small><?= StringHelper::truncate($lastChatMessage, 40, '...')?></small></p>
                    <?php endif; ?>
                <?php \yii\widgets\Pjax::end() ?>
            </span>
        </div>
        <div class="_cc_item_data">
            <?php if ($clientChat->cchDep): ?>
                <span class="label label-info"><?= Html::encode($clientChat->cchDep->dep_name) ?></span>
            <?php endif; ?>

            <?php if ($clientChat->cchProject): ?>
                <span class="label label-success"><?= Html::encode($clientChat->cchProject->name) ?></span>
            <?php endif; ?>

            <span class="label label-default"><?= Html::encode($clientChat->cchChannel->ccc_name) ?></span>

			<?php if ($lastChatMessageDate): ?>
                <span title="Last chat message">
                    <?php $period = round((time() - strtotime($lastChatMessageDate))); ?>
					<small class="_cc-item-last-message-time" data-moment="<?= $period ?>" data-cch-id="<?= $clientChat->cch_id ?>"></small><br>
                </span>
			<?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
