<?php

namespace common\models\query;

use common\models\Quote;
use yii\db\ActiveQuery;

class QuoteQuery extends ActiveQuery
{
    public function originalExist(int $leadId): bool
    {
        return $this->andWhere(['lead_id' => $leadId])->andWhere(['q_type_id' => Quote::TYPE_ORIGINAL])->exists();
    }
}
