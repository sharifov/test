<?php

namespace common\models\query;

use common\models\ConferenceParticipant;

class ConferenceParticipantQuery extends \yii\db\ActiveQuery
{
    public function byAgent(): self
    {
        return $this->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT]);
    }
}
