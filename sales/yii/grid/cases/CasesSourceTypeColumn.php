<?php

namespace sales\yii\grid\cases;

use sales\entities\cases\CasesSourceType;
use yii\grid\DataColumn;

/**
 * Class CasesSourceTypeColumn
 *
 * Ex.
        [
            'class' => \sales\yii\grid\cases\CasesSourceTypeColumn::class,
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
