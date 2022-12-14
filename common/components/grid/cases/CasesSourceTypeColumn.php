<?php

namespace common\components\grid\cases;

use src\entities\cases\CasesSourceType;
use yii\grid\DataColumn;

/**
 * Class CasesSourceTypeColumn
 *
 * Ex.
        [
            'class' => \common\components\grid\cases\CasesSourceTypeColumn::class,
            'attribute' => 'cs_source_type_id',
        ],
 */
class CasesSourceTypeColumn extends DataColumn
{
    public $format = 'casesSourceType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CasesSourceType::getList();
        }
    }
}
