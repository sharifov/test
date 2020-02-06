<?php

namespace modules\offer\src\grid\columns;

use modules\offer\src\entities\offer\OfferStatusAction;
use yii\grid\DataColumn;

/**
 * Class OfferStatusActionColumn
 *
 * Ex.
        [
            'class' => \modules\offer\src\grid\columns\OfferStatusActionColumn::class,
            'attribute' => 'osl_action_id'
        ],
 */
class OfferStatusActionColumn extends DataColumn
{
    public $format = 'offerStatusAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OfferStatusAction::getList();
        }
    }
}
