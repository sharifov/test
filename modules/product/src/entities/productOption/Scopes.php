<?php

namespace modules\product\src\entities\productOption;

/**
 * @see ProductOption
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['po_enabled' => true]);
    }
}
