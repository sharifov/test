<?php

namespace sales\yii\grid\quote;

use common\models\Quote;
use yii\grid\DataColumn;

/**
 * Class QuoteTypeColumn
 *
 *  Ex.
    ['class' => \sales\yii\grid\quote\QuoteTypeColumn::class],
 *
 */
class QuoteTypeColumn extends DataColumn
{
    public $format = 'quoteType';
    public $filter = Quote::TYPE_LIST;
    public $attribute = 'type_id';
}
