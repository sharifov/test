<?php

namespace common\components\grid\clientNotification;

use sales\model\client\notifications\client\entity\NotificationType;
use sales\model\client\notifications\phone\entity\Status;
use yii\grid\DataColumn;

/**
 * Class ClientNotificationPhoneListStatusColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\clientNotification\ClientNotificationPhoneListStatusColumn::class,
        'attribute' => 'cnfl_status_id',
    ],
 *
 */
class ClientNotificationPhoneListStatusColumn extends DataColumn
{
    public $format = 'clientNotificationPhoneListStatus';
    public $attribute = 'cnfl_status_id';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = Status::getList();
        }
    }
}
