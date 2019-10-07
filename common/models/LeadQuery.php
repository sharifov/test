<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * Class LeadQuery
 */
class LeadQuery extends ActiveQuery
{

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['status' => [
            Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
        ]]);
    }

}
