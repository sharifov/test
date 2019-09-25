<?php

namespace sales\forms\leadflow;

use common\models\Lead;
use yii\base\Model;

class ReasonForm extends Model
{

    public const STATUS_REASON_LIST = [
        Lead::STATUS_TRASH => [
            1 => 'Purchased elsewhere',
            2 => 'Duplicate',
            3 => 'Travel dates passed',
            4 => 'Invalid phone number',
            5 => 'Canceled trip',
            6 => 'Test',
            0 => 'Other'
        ],
        Lead::STATUS_REJECT => [
            1 => 'Purchased elsewhere',
            2 => 'Flight date > 10 months',
            3 => 'Not interested',
            4 => 'Duplicate',
            5 => 'Too late',
            6 => 'Test',
            0 => 'Other'
        ],
        Lead::STATUS_FOLLOW_UP => [
            1 => 'Proper Follow Up Done',
            2 => "Didn't get in touch",
            0 => 'Other'
        ],
        Lead::STATUS_PROCESSING => [
            1 => 'N/A',
            2 => 'No Available',
            3 => 'Voice Mail Send',
            4 => 'Will call back',
            5 => 'Waiting the option',
            0 => 'Other'
        ],
        Lead::STATUS_ON_HOLD => [
            0 => 'Other'
        ],
        Lead::STATUS_SNOOZE => [
            1 => 'Travelling dates > 12 months',
            2 => 'Not ready to buy now',
            0 => 'Other'
        ],
    ];

    /**
     * @param int $statusId
     * @param int $reasonId
     * @return string
     */
    public static function getReasonByStatus(int $statusId = 0, int $reasonId = 0): string
    {
        return self::STATUS_REASON_LIST[$statusId][$reasonId] ?? '-';
    }

    /**
     * @param int $statusId
     * @return array
     */
    public static function getReasonListByStatus(int $statusId = 0): array
    {
        return self::STATUS_REASON_LIST[$statusId] ?? [];
    }

}
