<?php

namespace sales\services\badges;

use sales\services\badges\objects\CasesBadgeCounter;
use sales\services\badges\objects\LeadBadgeCounter;
use sales\services\badges\objects\OrderBadgeCounter;
use sales\services\badges\objects\QaTaskBadgeCounter;
use sales\services\badges\objects\VoiceMailRecordBadgeCounter;
use Yii;

/**
 * Class BadgesDictionary
 */
class BadgesObjectFactory
{
    private string $keyObject;

    public function __construct(string $keyObject)
    {
        $this->keyObject = $keyObject;
    }

    public function create(): BadgeCounterInterface
    {
        switch ($this->keyObject) {
            case BadgesDictionary::KEY_OBJECT_LEAD:
                return Yii::createObject(LeadBadgeCounter::class);
            case BadgesDictionary::KEY_OBJECT_CASES:
                return Yii::createObject(CasesBadgeCounter::class);
            case BadgesDictionary::KEY_OBJECT_VOICE_MAIL:
                return new VoiceMailRecordBadgeCounter();
            case BadgesDictionary::KEY_OBJECT_ORDER:
                return new OrderBadgeCounter();
            case BadgesDictionary::KEY_OBJECT_QA_TASK:
                return Yii::createObject(QaTaskBadgeCounter::class);
        }
        throw new \RuntimeException('keyObject (' . $this->keyObject . ') unprocessed');
    }
}
