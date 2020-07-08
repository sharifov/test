<?php

use sales\helpers\clientChat\ClientChatMessageHelper;
use sales\model\clientChat\entity\ClientChat;
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

$countMessages = ClientChatMessageHelper::getCountOfChatUnreadMessage($clientChat->cch_id, $clientChat->cch_owner_user_id);
?>

<?php if ($countMessages): ?>
<li>
	<a href="<?= Url::to(['/client-chat/index', 'chid' => $clientChat->cch_id]) ?>">
		<span class="glyphicon glyphicon-info-sign"> </span>
		<span>
            <span>You have unread messages: <?= Html::tag('span', $countMessages, ['class' => 'label label-default']) ?> from <?= $clientChat->cchClient->full_name ?></span>
        </span>
        <?php if ($message): ?>
        <span class="message"><?= $message ?></span>
        <?php endif; ?>
    </a>
</li>
<?php endif; ?>
