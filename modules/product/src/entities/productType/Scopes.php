<?php

namespace modules\product\src\entities\productType;

/**
 * @see ProductType
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['pt_enabled' => true]);
    }
}
