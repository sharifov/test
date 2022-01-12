<?php

namespace common\components\grid\clientNotification;

use src\model\client\notifications\client\entity\NotificationType;
use yii\grid\DataColumn;

/**
 * Class ClientNotificationTypeColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\clientNotification\ClientNotificationTypeColumn::class,
        'attribute' => 'cn_notification_type_id',
    ],
 *
 */
class ClientNotificationTypeColumn extends DataColumn
{
    public $format = 'clientNotificationType';
    public $attribute = 'cn_notification_type_id';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = NotificationType::getList();
        }
    }
}
