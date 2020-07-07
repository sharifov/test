<?php

use common\models\Notifications;
use frontend\widgets\notification\NotificationSocketAsset;
use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var Notifications[] $notifications */
/* @var ClientChat[] $chatsWithUnreadMessages */
/* @var integer $count */
/* @var integer $totalUnreadMessages */
/** @var View $this */

if (!$count) {
    $count = null;
}

NotificationSocketAsset::register($this);

?>

<li class="dropdown open" role="presentation">
    <a href="javascript:;" class="dropdown-toggle info-number" title="Chat Notifications" data-toggle="dropdown"
       aria-expanded="false" >
        <i class="fa fa-comments"></i><span class="badge bg-green _cc_unread_messages"><?= $totalUnreadMessages ?></span>
    </a>

    <ul id="cc-notification-menu" class="dropdown-menu list-unstyled msg_list" role="menu" x-placement="bottom-end">

        <?php if ($chatsWithUnreadMessages): ?>
            <?php foreach ($chatsWithUnreadMessages as $clientChat): ?>
                <?= $this->render('item_cc', [
                    'clientChat' => $clientChat
                ]) ?>
            <?php endforeach; ?>

        <?php else: ?>
            <li>
                <div class="text-center">
                    <?= Html::a('<i class="fa fa-warning"></i> <strong>You have no new notifications</strong>', ['#']) ?>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</li>

<li class="dropdown open" role="presentation">

    <a href="javascript:;" onclick="notificationUpdateTime();" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown"
       aria-expanded="false">
        <i class="fa fa-bell-o"></i><span class="badge bg-green notification-counter"><?= $count ?></span>
    </a>

    <ul id="notification-menu" class="dropdown-menu list-unstyled msg_list" role="menu" x-placement="bottom-end">

        <?php foreach ($notifications as $notification): ?>
            <?= $this->render('item', [
                'id' => $notification->n_id,
                'title' => $notification->n_title,
                'createdDt' => $notification->n_created_dt,
                'message' => $notification->n_message
            ]) ?>
        <?php endforeach; ?>

        <li>
            <div class="text-center">
                <?= Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list']) ?>
            </div>
        </li>
    </ul>

</li>

<?php

$this->registerJs("notificationCount('" . $count . "', '".$totalUnreadMessages."')", View::POS_END);
