<?php

namespace common\models\query;

use common\models\Sources;

/**
 * This is the ActiveQuery class for [[Sources]].
 *
 * @see Sources
 */
class SourcesQuery extends \yii\db\ActiveQuery
{

    public function byCid(string $cid): self
    {
        return $this->andWhere(['cid' => $cid]);
    }

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['hidden' => false]);
    }
}
