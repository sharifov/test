<?php

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var Notifications[] $notifications */
/* @var ClientChat[] $chatsWithUnreadMessages */
/* @var integer $count */
/* @var integer $totalUnreadMessages */
/** @var View $this */

if (!$count) {
    $count = null;
}

?>



<?php Pjax::begin(['id' => 'notify-pjax-cc', 'timeout' => false, 'enablePushState' => false, 'enableReplaceState' => false, 'options' => [
    'tag' => 'li',
    'class' => 'dropdown open',
    'role' => 'presentation',
]])?>
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

<?php Pjax::end() ?>

<?php Pjax::begin(['id' => 'notify-pjax', 'timeout' => false, 'enablePushState' => false, 'enableReplaceState' => false, 'options' => [
    'tag' => 'li',
    'class' => 'dropdown open',
    'role' => 'presentation',
]])?>

    <?php $pNotifiers = null; ?>

    <a href="javascript:;" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown"
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
            <?php
                if ($notification->isMustPopupShow()) {
                    $title = Html::encode($notification->n_title);
                    $type = $notification->getNotifyType();
                    $message = str_replace(["\r\n", "\n", '"'], ['', '', '\"'], $notification->n_message);
                    $desktopMessage = str_replace('"', '\"', strip_tags($notification->n_message));
                    $pNotifiers .= "notificationPNotify('" . $type . "', '" . $title . "', '" . $message . "', '" . $desktopMessage . "');" . PHP_EOL;
                }
            ?>
        <?php endforeach; ?>

        <?php $this->registerJs($pNotifiers, View::POS_END); ?>
        <?php $this->registerJs('notificationCount(\'' . $count . '\');', View::POS_END); ?>

        <li>
            <div class="text-center">
                <?= Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list']) ?>
            </div>
        </li>
    </ul>

<?php yii\widgets\Pjax::end() ?>

<?php

$notifyUrl = Url::to(['/notifications/pjax-notify']);

$js = <<<JS

const notifyUrl = '$notifyUrl';
function updatePjaxNotify() {
    $.pjax.reload({url: notifyUrl, container : '#notify-pjax', push: false, replace: false, timeout: 10000, scrollTo: false, async: false});
}

function updatePjaxCcNotify() {
    $.pjax.reload({url: notifyUrl, container : '#notify-pjax-cc', push: false, replace: false, timeout: 10000, scrollTo: false, async: false});
}

$("#notify-pjax").on("pjax:beforeSend", function() {
    $('#notify-pjax .info-number i').removeClass('fa-comment-o').addClass('fa-spin fa-spinner');
});

$("#notify-pjax").on("pjax:complete", function() {
    $('#notify-pjax .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
});

$("#notify-pjax").on('pjax:timeout', function(event) {
    $('#notify-pjax .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
    event.preventDefault()
});


$("#notify-pjax-cc").on("pjax:beforeSend", function() {
    $('#notify-pjax-cc .info-number i').removeClass('fa-comment-o').addClass('fa-spin fa-spinner');
});

$("#notify-pjax-cc").on("pjax:complete", function() {
    $('#notify-pjax-cc .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
});

$("#notify-pjax-cc").on('pjax:timeout', function(event) {
    $('#notify-pjax-cc .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-comment-o');
    event.preventDefault()
});
 
function notificationPNotify(type, title, message, desktopMessage) {
    new PNotify({
        type: type,
        title: title,
        text: message,
        icon: true,
        desktop: {
            desktop: true,
            fallback: true,
            text: desktopMessage
        },
        delay: 10000,
        mouse_reset: false,
        hide: true
    });
    soundNotification();
}

function notificationCount(count) {
    $(".notification-counter").text(count);   
}

JS;

$this->registerJs($js, View::POS_END);
