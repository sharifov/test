<?php

namespace modules\offer\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class OfferColumn
 *
 * @property $relation
 *
 * Ex.
        [
            'class' => \modules\offer\src\grid\columns\OfferColumn::class,
            'attribute' => 'osl_offer_id',
            'relation' => 'offer',
        ],
 */
class OfferColumn extends DataColumn
{
    public $relation;

    public function init(): void
    {
        parent::init();
        if (!$this->relation) {
            throw new \InvalidArgumentException('relation must be set.');
        }
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        if ($model->{$this->attribute}) {
            return $this->grid->formatter->format($model->{$this->relation}, 'offer');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
