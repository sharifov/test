<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;

/** @var $chat ClientChat */

$start = Yii::$app->formatter->asDatetime(strtotime($chat->cch_created_dt));
$owner = $chat->hasOwner() ? $chat->cchOwnerUser->nickname : '';
$feedback = $chat->feedback ? $chat->feedback->ccf_rating : '';
$channelName = $chat->cchChannel ? $chat->cchChannel->ccc_name : '';
$status = $chat->getStatusName();
$count = ClientChatMessage::countByChatId($chat->cch_id);
$feedback = '';
if ($chat->feedback) {
    for ($i = 0; $i < $chat->feedback->ccf_rating; $i++) {
        $feedback .= '<i class="fa fa-star text-warning"> </i>';
    }
}
$lastMessageDate = $chat->lastMessage ? Yii::$app->formatter->asRelativeTime(strtotime($chat->lastMessage->cclm_dt)) : '';
$duration = '';
if ($lastMessageDate) {
    $durationTime = strtotime($lastMessageDate) - strtotime($chat->cch_created_dt);
    $duration = Yii::$app->formatter->asDuration($durationTime);
}

?>
<div class="chat__message chat__message--client chat__message--phone">
    <div class="chat__icn"><i class="fa fa-comments-o"> </i></div>
    <?php // <i class="chat__status chat__status--success fa fa-circle" data-toggle="tooltip" title="" data-placement="right" data-original-title="COMPLETED - 26-Mar-2020 [19:31] - Call ID: 3363432"></i>?>
    <div class="chat__message-heading">
        <div class="chat__sender">
            <?= $chat->cchChannel->ccc_name ?> chat from <?= $chat->cchClient->getShortName()?>
        </div>
        <div class="chat__date">
            Id: <?= $chat->cch_id ?>
            <i class="fa fa-calendar"> </i> <?= $start ?></div>
    </div>
    <div class="card-body">
        <table class="table table-condensed" style="background-color: rgba(255, 255,255, .7)">
            <tbody>
            <tr>
                <td style="width:80px">
                    <?= $channelName ?>
                </td>
                <td class="text-left">
                    <?php //<i class="fa fa-flag text-success"> </i>?> <?= $status ?>
                </td>
                <td class="text-center" style="width: 70px">
                    <i class="fa fa-comments-o"> </i> <?= $count ?>
                </td>
                <td>
                    <span class="badge badge-warning"><?= $duration ?></span>
                </td>
                <td class="text-center">
                    <small><?= $lastMessageDate ?></small>
                </td>
                <td class="text-center" style="width: 90px">
                    <?= $feedback ?>
                </td>
                <td class="text-left" style="width:150px">
                    <div><i class="fa fa-user fa-border"> </i> <?= $owner ?></div>

                </td>
            </tr>
            </tbody>
        </table>
        <div class="chat__message-footer">
            <a class="chat__details" href="#"><i class="fa fa-search-plus"> </i>Details</a>
        </div>
    </div>
</div>
