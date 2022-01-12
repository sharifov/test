<?php

namespace src\services\badges\objects;

use src\auth\Auth;
use src\model\voiceMailRecord\entity\VoiceMailRecord;
use src\services\badges\BadgeCounterInterface;

/**
 * Class VoiceMailRecordBadgeCounter
 */
class VoiceMailRecordBadgeCounter implements BadgeCounterInterface
{
    public function countTypes(array $types): array
    {
        return [
            'count' => VoiceMailRecord::find()->andWhere(['vmr_user_id' => Auth::id(), 'vmr_new' => true, 'vmr_deleted' => false])->count()
        ];
    }
}
