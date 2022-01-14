<?php

namespace modules\product\src\entities\productOption;

use src\helpers\setting\SettingHelper;

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
        return $this->andWhere(['IN', 'pq_status_id', SettingHelper::getProductQuoteChangeableStatuses()]);
    }
}
