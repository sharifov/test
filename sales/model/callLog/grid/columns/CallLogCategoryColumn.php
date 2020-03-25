<?php

namespace sales\model\callLog\grid\columns;

use sales\model\callLog\entity\callLog\CallLogCategory;
use yii\grid\DataColumn;

/**
 * Class CallLogCategoryColumn
 *
 * Ex.
        [
            'class' => \sales\model\callLog\grid\columns\CallLogCategoryColumn::class,
            'attribute' => 'cl_category_id',
        ],
 */
class CallLogCategoryColumn extends DataColumn
{
    public $format = 'callLogCategory';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CallLogCategory::getList();
        }

        if (!$this->attribute) {
            $this->attribute = 'cl_category_id';
        }
    }
}
