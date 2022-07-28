<?php

namespace src\model\leadBusinessExtraQueueRule\entity;

use yii\db\Expression;

class LeadBusinessExtraQueueRuleQuery
{
    public static function timeIntersectionCheck(string $startTime, string $endTime, int $lbeqrTypeId, ?int $lbeqrId): bool
    {
        return (new \yii\db\Query())
            ->from('{{%lead_business_extra_queue_rules}}')
            ->where(['lbeqr_type_id' => $lbeqrTypeId])
            ->andWhere(['<>', 'lbeqr_id', $lbeqrId])
            ->andWhere([
                'OR',
                [
                    'AND',
                    [
                        '>=',
                        new Expression("STR_TO_DATE(`lbeqr_start_time`, '%H:%i')"),
                        new Expression("STR_TO_DATE('" . $startTime . "', '%H:%i')"),
                    ],
                    [
                        '<=',
                        new Expression("STR_TO_DATE(`lbeqr_start_time`, '%H:%i')"),
                        new Expression("STR_TO_DATE('" . $endTime . "', '%H:%i')"),
                    ]
                ],
                [
                    'AND',
                    [
                        '<=',
                        new Expression("STR_TO_DATE(`lbeqr_end_time`, '%H:%i')"),
                        new Expression("STR_TO_DATE('" . $endTime . "', '%H:%i')"),
                    ],
                    [
                        '>=',
                        new Expression("STR_TO_DATE(`lbeqr_end_time`, '%H:%i')"),
                        new Expression("STR_TO_DATE('" . $startTime . "', '%H:%i')"),
                    ]
                ],
            ])
            ->exists();
    }

    public static function getRepeatedRule(): ?LeadBusinessExtraQueueRule
    {
        return LeadBusinessExtraQueueRule
            ::find()
            ->where(['lbeqr_type_id' => LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE])
            ->one();
    }

    public static function getRuleByClientTime(string $time)
    {
        return LeadBusinessExtraQueueRule
            ::find()
            ->where(['lbeqr_type_id' => LeadBusinessExtraQueueRule::TYPE_ID_DEFAULT_RULE])
            ->andWhere([
                '<=',
                new Expression("STR_TO_DATE(`lbeqr_start_time`, '%H:%i')"),
                new Expression("STR_TO_DATE('" . $time . "', '%H:%i')")
            ])
            ->andWhere([
                '>=',
                new Expression("STR_TO_DATE(`lbeqr_end_time`, '%H:%i')"),
                new Expression("STR_TO_DATE('" . $time . "', '%H:%i')")
            ])
            ->one();
    }
}
