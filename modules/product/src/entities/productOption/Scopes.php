<?php

namespace modules\product\src\entities\productOption;

use sales\helpers\setting\SettingHelper;

/**
 * @see ProductOption
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['po_enabled' => true]);
    }

    public function changeable(): self
    {
        $quoteChangeableStatuses = array_keys(SettingHelper::getProductQuoteChangeableStatuses());
        return $this->andWhere(['IN', 'pq_status_id', $quoteChangeableStatuses]);
    }
}
