<?php

namespace common\components\grid\clientNotification;

use sales\model\client\notifications\client\entity\CommunicationType;
use yii\grid\DataColumn;

/**
 * Class ClientNotificationCommunicationTypeColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\clientNotification\ClientNotificationCommunicationTypeColumn::class,
        'attribute' => 'cn_notification_type_id',
    ],
 *
 */
class ClientNotificationCommunicationTypeColumn extends DataColumn
{
    public $format = 'clientNotificationCommunicationType';
    public $attribute = 'cn_communication_type_id';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CommunicationType::getList();
        }
    }
}
