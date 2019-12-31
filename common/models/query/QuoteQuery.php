<?php

namespace common\models\query;

use common\models\Quote;
use yii\db\ActiveQuery;

class QuoteQuery extends ActiveQuery
{
    public function originalExist(int $leadId, int $excludeQuoteId = null): bool
    {
        $query = $this
            ->andWhere(['lead_id' => $leadId])
            ->andWhere(['type_id' => Quote::TYPE_ORIGINAL]);
        if ($excludeQuoteId) {
            $query->andWhere(['<>', 'id', $excludeQuoteId]);
        }
        return $query->exists();
    }
}
