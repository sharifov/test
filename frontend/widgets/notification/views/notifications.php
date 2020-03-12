<?php

use common\models\Notifications;
use yii\helpers\Html;
use yii\web\View;

/* @var Notifications[] $notifications */
/* @var integer $newCount */
/** @var View $this */

?>
    <li class="dropdown open" role="presentation">

        <a href="javascript:;" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-comment-o"></i>
            <?php if ($newCount): ?>
                <span class="badge bg-green"><?= $newCount ?></span>
            <?php endif; ?>
        </a>

        <ul id="notification-menu" class="dropdown-menu list-unstyled msg_list" role="menu" x-placement="bottom-end">
            <?php foreach ($notifications as $n => $notification): ?>
                <?= $this->render('item', [
                    'id' => $notification->n_id,
                    'title' => $notification->n_title,
                    'createdDt' => $notification->n_created_dt,
                    'message' => $notification->n_message,
                ]) ?>
            <?php endforeach; ?>
            <li>
                <div class="text-center">
                    <?= Html::a('<i class="fa fa-search"></i> <strong>See all Notifications</strong>', ['/notifications/list'], ['data-pjax' => 0]) ?>
                </div>
            </li>
        </ul>
    </li>
<?php

$js = <<<JS
function notificationInit(data) {
    console.log('notificationInit.start');
    console.log(data);
    try {
        var command = data['command'];
        var message = data['message'];
    } catch (error) {
        console.error('Invalid data on notificationInit');
        console.error(data);
        return;
    }

    if (command === 'add') {
        notificationAdd(message);
    }
    notificationUpdateTime();
   // notificationUpdateList()//todo
}

function notificationAdd(message) {
    $("#notification-menu").prepend('<li>we</li>');
}
function notificationUpdateTime() {
    $( "#notification-menu li .time").each(function() {
        $(this).text(timeDifference(new Date(), new Date($(this).data('time') * 1000)));
    });
}

function timeDifference(current, previous) {

    let msPerMinute = 60 * 1000;
    let msPerHour = msPerMinute * 60;
    let msPerDay = msPerHour * 24;
    let msPerMonth = msPerDay * 30;
    let msPerYear = msPerDay * 365;

    let elapsed = current - previous;

    if (elapsed < msPerMinute) {
         return Math.round(elapsed/1000) + ' seconds ago';   
    } else if (elapsed < msPerHour) {
         return Math.round(elapsed/msPerMinute) + ' minutes ago';   
    } else if (elapsed < msPerDay ) {
         return Math.round(elapsed/msPerHour ) + ' hours ago';   
    } else if (elapsed < msPerMonth) {
        return 'approximately ' + Math.round(elapsed/msPerDay) + ' days ago';   
    } else if (elapsed < msPerYear) {
        return 'approximately ' + Math.round(elapsed/msPerMonth) + ' months ago';   
    } else {
        return 'approximately ' + Math.round(elapsed/msPerYear ) + ' years ago';   
    }
}

JS;

$this->registerJs($js, View::POS_READY);
