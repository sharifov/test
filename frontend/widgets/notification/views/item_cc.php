<?php

use src\helpers\clientChat\ClientChatMessageHelper;
use src\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var ClientChat $clientChat */

$message = '';
if ($clientChat->cchDep) {
    $message .= Html::tag('span', $clientChat->cchDep->dep_name, ['class' => 'label label-info']) . ' ';
}
if ($clientChat->cchProject) {
    $message .= Html::tag('span', $clientChat->cchProject->name, ['class' => 'label label-success']);
}

?>

<li>
    <a href="<?= Url::to(['/client-chat/dashboard-v2', 'chid' => $clientChat->cch_id]) ?>" data-pjax="0">
        <span class="glyphicon glyphicon-info-sign"> </span>
        <span>
            <span>You have unread messages: <?= Html::tag('span', $clientChat->countUnreadMessage, ['class' => 'label label-default']) ?> from <?= $clientChat->cchClient->full_name ?></span>
        </span>
        <?php if ($message) : ?>
        <span class="message"><?= $message ?></span>
        <?php endif; ?>
    </a>
</li>
