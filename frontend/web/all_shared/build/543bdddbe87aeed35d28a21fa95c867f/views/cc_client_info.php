<?php

use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;

/** @var ClientChat $clientChat */

$client = $clientChat->cchClient;

?>
<div class="col-md-12">
    <div style="display: flex; margin-bottom: 15px;">
        <span class="_rc-client-icon _cc-item-icon-round">
            <span class="_cc_client_name"><?= ClientChatHelper::getFirstLetterFromName(ClientChatHelper::getClientName($clientChat)) ?></span>
            <span class="_cc-status-wrapper"><span class="_cc-status" data-is-online="<?= (int)$clientChat->cch_client_online ?>"> </span></span>
        </span>
        <div class="_rc-client-info">
            <span class="_rc-client-name"><span><?= Html::encode($client->full_name ?: 'Client-' . $client->id) ?></span></span>

            <?php if ($emails = $client->clientEmails) : ?>
                <span class="_rc-client-email"> <i class="fa fa-envelope"> </i>
                <?php foreach ($emails as $email) : ?>
                    <code><?= Html::encode($email->email) ?></code>
                <?php endforeach; ?>
                </span>
            <?php endif; ?>

            <?php if ($phones = $client->clientPhones) : ?>
                <span class="_rc-client-phone"><i class="fa fa-phone"> </i>
                <?php foreach ($phones as $phone) : ?>
                    <code><?= Html::encode($phone->phone) ?></code>
                <?php endforeach; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
