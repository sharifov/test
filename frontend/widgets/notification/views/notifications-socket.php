<?php

use common\models\Notifications;
use frontend\widgets\notification\NotificationSocketAsset;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var Notifications[] $notifications */
/* @var integer $count */
/** @var View $this */

if (!$count) {
    $count = null;
}

NotificationSocketAsset::register($this);

?>

<li class="dropdown open" role="presentation">

    <a href="javascript:;" onclick="notificationUpdateTime();" class="dropdown-toggle info-number" title="Notifications" data-toggle="dropdown"
       aria-expanded="false">
        <i class="fa fa-comment-o"></i><span class="badge bg-green notification-counter"><?= $count ?></span>
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

$this->registerJs("notificationCount('" . $count . "')", View::POS_END);
