<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\processingQuality;

use common\models\Call;
use common\models\Lead;
use common\models\LeadFlow;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class QaTaskCreateLeadProcessingQualityQuery
{
    public static function getLeads(Rule $rule, int $categoryId): array
    {
        $nowDt = date('Y-m-d H:i:s');

        $callAttempt = self::getCallAttemptsQuery($rule);

        $leads = Lead::find()->alias('l')
            ->select(['l.id'])
            ->addSelect(['times' =>
                new Expression("TIMESTAMPDIFF(HOUR, lfs.created, '" . $nowDt . "') - " . $rule->hour_offset)
            ])
            ->addSelect(['attempts_hf1' =>
                self::createAttemptsQuery($callAttempt, ($rule->hour_frame_1 + $rule->hour_offset))
            ])
            ->addSelect(['attempts_hf2' =>
                self::createAttemptsQuery($callAttempt, ($rule->hour_frame_2 + $rule->hour_offset))
            ])
            ->addSelect(['attempts_hf3' =>
                self::createAttemptsQuery($callAttempt, ($rule->hour_frame_3 + $rule->hour_offset))
            ])
            ->innerJoin(
                LeadFlow::tableName() . ' as lfs',
                'l.id = lfs.lead_id'
                . ' AND lfs.status = ' . Lead::STATUS_PROCESSING . ' AND lfs.lf_from_status_id = ' . Lead::STATUS_PENDING
                . ' AND lfs.created > \'' . (new \DateTimeImmutable())->modify('-' . ($rule->hour_frame_3 + $rule->hour_offset) . ' hour')->format('Y-m-d H:i:s') . '\''
            )
            ->andWhere(['l.status' => Lead::STATUS_PROCESSING])
            ->andWhere(
                ' ( '
                . LeadFlow::find()->alias('lf')->select(new Expression("concat(lf.lf_from_status_id, '-', lf.status)"))
                    ->andWhere('l.id = lf.lead_id')
                    ->orderBy(['lf.id' => SORT_DESC])
                    ->limit(1)->createCommand()->getRawSql()
                . ' ) = \'1-2\''
            )
            ->andWhere(
                ' ( '
                . QaTask::find()->alias('qt')->select('count(*)')
                    ->andWhere(['qt.t_object_type_id' => QaTaskObjectType::LEAD])
                    ->andWhere(['qt.t_category_id' => $categoryId])
                    ->andWhere('qt.t_object_id = l.id')
                    ->createCommand()->getRawSql()
                . ' ) = 0'
            )
            ->andWhere(['>', 'l.created', (new \DateTimeImmutable())->modify('-' . ($rule->hour_frame_3 + $rule->hour_offset) . ' hour')->format('Y-m-d H:i:s')])
            ->andHaving(['OR',
                new Expression('(times >= 0 AND times < ' . $rule->hour_frame_1 . ') AND attempts_hf1 < ' . $rule->calls_per_frame),
                new Expression(
                    '(times >= ' . $rule->hour_frame_1 . ' AND times < ' . $rule->hour_frame_2 . ') '
                    . 'AND (attempts_hf1 < ' . $rule->calls_per_frame . ' OR attempts_hf2 < ' . ($rule->calls_per_frame * 2) . ')'
                ),
                new Expression(
                    '(times >= ' . $rule->hour_frame_2 . ' AND times < ' . $rule->hour_frame_3 . ') '
                    . 'AND (attempts_hf1 < ' . $rule->calls_per_frame . ' OR attempts_hf2 < ' . ($rule->calls_per_frame * 3) . ')'
                )
            ])
//            ->createCommand()->getRawSql();
            ->asArray()->all();

        return ArrayHelper::getColumn($leads, 'id');
    }

    private static function getCallAttemptsQuery(Rule $rule): ActiveQuery
    {
        $inCallsCondition = [];
        if ($rule->include_in_calls) {
            $inCallsCondition = ['AND',
                ['c_call_type_id' => Call::CALL_TYPE_IN],
                ['>=', 'c_recording_duration', $rule->in_min_rec_duration]
            ];
        }
        $callAttempt = Call::find()->select('count(*)')
            ->andWhere('c_lead_id = l.id')
            ->andWhere('c_created_dt > lfs.created')
            ->andWhere(['!=', 'c_source_type_id', Call::SOURCE_TRANSFER_CALL])
            ->andWhere(['IS NOT', 'c_parent_id', null])
            ->andWhere(['OR',
                ['AND',
                    ['c_call_type_id' => Call::CALL_TYPE_OUT],
                    ['>=', 'c_call_duration', $rule->out_min_duration]
                ],
                $inCallsCondition
            ]);
        return $callAttempt;
    }

    private static function createAttemptsQuery(ActiveQuery $attempts, int $diff): ActiveQuery
    {
        $query = clone $attempts;
        $time = (new \DateTimeImmutable())->modify('-' . $diff . ' hour');
        $query->andWhere(['>', 'c_created_dt', $time->format('Y-m-d H:i:s')]);
        return $query;
    }
}
