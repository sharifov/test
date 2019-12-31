<?php

namespace sales\yii\grid\quote;

use common\models\Quote;
use yii\grid\DataColumn;

class QuoteTypeColumn extends DataColumn
{
    public $format = 'quoteType';
    public $filter = Quote::TYPE_LIST;
    public $attribute = 'q_type_id';
}
