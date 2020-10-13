<?php

use common\components\i18n\Formatter;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $clientChats ClientChat[] */
/** @var $clientChatId int|null */
/** @var $formatter Formatter */
?>

<?php foreach ($clientChats as $clientChat): ?>
    <?php
        $inMessage = true;
        $lastChatMessage = $lastChatMessageDate = '';
        $lastClientMessage = ClientChatMessage::getLastMessageByClient((int)$clientChat['cch_id']);
        $lastAgentMessage = ClientChatMessage::getLastMessageByAgent((int)$clientChat['cch_id']);

        if ($lastClientMessage && !$lastAgentMessage) {
            $inMessage = true;
            $lastChatMessage = $lastClientMessage->message;
            $lastChatMessageDate = $lastClientMessage->ccm_sent_dt;
        } elseif ($lastAgentMessage && !$lastClientMessage) {
            $inMessage = false;
            $lastChatMessage = $lastAgentMessage->message;
            $lastChatMessageDate = $lastAgentMessage->ccm_sent_dt;
        } elseif ($lastClientMessage && $lastAgentMessage) {
            if (strtotime($lastClientMessage->ccm_sent_dt) > strtotime($lastAgentMessage->ccm_sent_dt)) {
                $inMessage = true;
                $lastChatMessage = $lastClientMessage->message;
                $lastChatMessageDate = $lastClientMessage->ccm_sent_dt;
            } else {
                $inMessage = false;
                $lastChatMessage = $lastAgentMessage->message;
                $lastChatMessageDate = $lastAgentMessage->ccm_sent_dt;
            }
        }
        $isClosed = (int)$clientChat['cch_status_id'] === ClientChat::STATUS_CLOSED;
    ?>

        <div id="dialog-<?= $clientChat['cch_id'] ?>" data-owner-id="<?= $clientChat['cch_owner_user_id'] ?>" class="_cc-list-item <?= $isClosed ? 'cc_closed' : '' ?> <?= $clientChatId && $clientChatId === (int)$clientChat['cch_id'] ? '_cc_active' : '' ?>" data-goto-param="/live/<?= $clientChat['cch_rid'] ?>?layout=embedded" data-rid="<?= $clientChat['cch_rid'] ?>" data-cch-id="<?= $clientChat['cch_id'] ?>" data-is-closed="<?= (int)$clientChat['cch_status_id'] === ClientChat::STATUS_CLOSED ?>">
        <div class="_cc-item-icon-wrapper">
            <span class="_cc-item-icon-round">
                <span class="_cc_client_name"><?= ClientChatHelper::getFirstLetterFromName($clientChat['client_full_name']) ?></span>
                <span class="_cc-status-wrapper">
                    <span class="_cc-status" data-is-online="<?= (int)$clientChat['cch_client_online'] ?>"></span>
                </span>
                <?php $unreadMessages = $clientChat['count_unread_messages'] ?: null; ?>
            </span>
            <span>
                <div title="Client name"><b><?= Html::encode($clientChat['client_full_name']) ?></b></div>
                <span title="Chat creation date"><small><?= $formatter->asByUserDateTime($clientChat['cch_created_dt'], 'php:F d Y, H:i') ?></small></span>
                <?php if (!empty($clientChat['cch_owner_user_id'])): ?>
                    , <span title="Owner"><small><i class="fa fa-user"></i> <?= Html::encode($clientChat['owner_username']) ?></small></span>
                <?php endif;?>
                <div>
                    <?php /*if ($clientChat['dep_name']): ?>
                        <span class="label label-info"><?= Html::encode($clientChat['dep_name']) ?></span>
                    <?php endif;*/ ?>

                    <?php if ($clientChat['project_name']): ?>
                        <span class="label label-success" title="Project"><?= Html::encode($clientChat['project_name']) ?></span>
                    <?php endif; ?>

                    <span class="label label-default" title="Channel"><?= Html::encode($clientChat['ccc_name']) ?></span>

                    <?php if ((int)$clientChat['cch_status_id'] === ClientChat::STATUS_TRANSFER):?>
                        <span class="label label-warning">In Transfer</span>
                    <?php endif; ?>
                </div>
                <?php // Pjax::begin(['id' => 'chat-last-message-refresh-' . $clientChat['cch_id']])?>
                <div id="chat-last-message-<?= $clientChat['cch_id'] ?>">
                    <?php if ($lastChatMessage) : ?>
                        <p title="Last <?= $inMessage ? 'client' : 'agent' ?>  message"><small><i class="fa fa-comment-o"></i> <?= StringHelper::truncate($lastChatMessage, 40, '...')?></small></p>
                    <?php endif; ?>
                </div>
                <?php // Pjax::end()?>
            </span>
        </div>
        <div class="_cc_item_data">
            <span class="label label-info">
            <?php
               echo Html::encode(ClientChat::getStatusNameById($clientChat['cch_status_id']));
            ?>
            </span>

			<?php if ($lastChatMessageDate): ?>
                <span title="Last message date & time">
                    <?php $period = round((time() - strtotime($lastChatMessageDate))); ?>
					<small class="_cc-item-last-message-time" data-moment="<?= $period ?>" data-cch-id="<?= $clientChat['cch_id'] ?>"></small><br>
                </span>
			<?php endif; ?>

            <span class="_cc-chat-unread-message" title="Unread messages">
                <span class="badge badge-info" data-cch-id="<?= $clientChat['cch_id'] ?>"><?=$unreadMessages>0 ? ($unreadMessages > 99 ? '99+' : $unreadMessages) : '' ?></span>
            </span>
        </div>
    </div>
<?php endforeach; ?>
