<?php

namespace modules\qaTask\src\entities\qaTaskRules;

/**
 * @see QaTaskRules
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byKey(string $key): self
    {
        return $this->andWhere(['tr_key' => $key]);
    }
}
