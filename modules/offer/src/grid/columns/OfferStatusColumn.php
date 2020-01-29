<?php

namespace modules\offer\src\grid\columns;

use modules\offer\src\entities\offer\OfferStatus;
use yii\grid\DataColumn;

/**
 * Class OfferStatusColumn
 *
 * Ex.
        [
            'class' => \modules\offer\src\grid\columns\OfferStatusColumn::class,
            'attribute' => 'status_id',
        ],
 */
class OfferStatusColumn extends DataColumn
{
    public $format = 'offerStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OfferStatus::getList();
        }
    }
}
