<?php

namespace sales\model\emailList\entity;

use yii\db\ActiveQuery;

/**
 * @see EmailList
 */
class Scopes extends ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['el_enabled' => true]);
    }
}
