<?php

namespace modules\offer\src\grid\columns;

use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use yii\grid\DataColumn;

/**
 * Class OfferSendLogTypeColumn
 *
 * Ex.
        [
            'class' => \modules\offer\src\grid\columns\OfferSendLogTypeColumn::class,
            'attribute' => 'type_id',
        ],
 */
class OfferSendLogTypeColumn extends DataColumn
{
    public $format = 'offerSendLogType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OfferSendLogType::getList();
        }
    }
}
