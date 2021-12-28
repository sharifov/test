<?php

namespace sales\services\badges\objects;

use sales\auth\Auth;
use sales\model\voiceMailRecord\entity\VoiceMailRecord;
use sales\services\badges\BadgeCounterInterface;

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
