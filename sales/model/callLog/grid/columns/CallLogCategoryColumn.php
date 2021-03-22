<?php

namespace sales\model\callLog\grid\columns;

use common\models\Call;
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
            $this->filter = Call::SOURCE_LIST;
        }

        if (!$this->attribute) {
            $this->attribute = 'cl_category_id';
        }
    }
}
