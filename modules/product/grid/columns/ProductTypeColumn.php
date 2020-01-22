<?php

namespace modules\product\grid\columns;

use yii\grid\DataColumn;

/**
 * Class ProductTypeColumn
 *
 * @property boolean $onlyEnabled
 */
class ProductTypeColumn extends DataColumn
{
    public $format = 'productType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = \common\models\ProductType::getListAll();
        }
    }
}
