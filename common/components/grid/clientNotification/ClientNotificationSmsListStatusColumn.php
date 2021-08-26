<?php

namespace common\components\grid\clientNotification;

use sales\model\client\notifications\sms\entity\Status;
use yii\grid\DataColumn;

/**
 * Class ClientNotificationSmsListStatusColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\clientNotification\ClientNotificationSmsListStatusColumn::class,
        'attribute' => 'cnsl_status_id',
    ],
 *
 */
class ClientNotificationSmsListStatusColumn extends DataColumn
{
    public $format = 'clientNotificationSmsListStatus';
    public $attribute = 'cnsl_status_id';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = Status::getList();
        }
    }
}
