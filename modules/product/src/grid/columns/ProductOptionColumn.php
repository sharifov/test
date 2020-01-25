<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productOption\ProductOption;
use modules\product\src\entities\productOption\ProductOptionQuery;
use yii\grid\DataColumn;

class ProductOptionColumn extends DataColumn
{
    public $relation;

    public function init(): void
    {
        parent::init();
        if (!$this->relation) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if ($this->filter === null) {
            $this->filter = ProductOptionQuery::getList();
        }
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        if ($model->{$this->attribute}) {
            /** @var ProductOption $productOption */
            $productOption = $model->{$this->relation};
            return $this->grid->formatter->format($productOption->po_name, 'ntext');
        }
        return $this->grid->formatter->format(null, 'ntext');
    }
}
