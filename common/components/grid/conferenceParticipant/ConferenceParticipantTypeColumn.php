<?php

namespace common\components\grid\conferenceParticipant;

use common\models\ConferenceParticipant;
use yii\grid\DataColumn;

class ConferenceParticipantTypeColumn extends DataColumn
{
    public $attribute  = 'cp_type_id';
    public $format = 'conferenceParticipantType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ConferenceParticipant::getTypeList();
        }
    }
}
