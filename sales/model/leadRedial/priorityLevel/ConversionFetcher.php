<?php

namespace sales\model\leadRedial\priorityLevel;

use common\models\Employee;
use common\models\Lead;
use common\models\ProfitSplit;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use yii\db\Expression;
use yii\db\Query;

class ConversionFetcher
{
    public function fetch(\DateTimeImmutable $fromDt, \DateTimeImmutable $toDt): array
    {
        $from = $fromDt->format('Y-m-d H:i:s');
        $to = $toDt->format('Y-m-d H:i:s');

        $query = (new Query())
            ->select([
                'users.id as user_id',
                'conversion_percent' => new Expression('if ((conversion_cnt is null or conversion_cnt = 0 or share is null or share = 0), 0, (share / conversion_cnt))')
            ])
            ->from(['users' => Employee::tableName()])
            ->leftJoin(['profit_split' => (new Query())
                ->select([
                    'ps_user_id as user_id',
                    'sum(ps_percent) as share',
                    'if (conversion_count is null, 0, conversion_count) as conversion_cnt',
                ])
                ->from(ProfitSplit::tableName())
                ->innerJoin(
                    ['l' => Lead::tableName()],
                    'l.id = ps_lead_id AND l.status = :status AND (DATE(l.l_status_dt) BETWEEN :from AND :to)',
                    [':status' => Lead::STATUS_SOLD, ':from' => $from, ':to' => $to]
                )
                ->leftJoin([
                    'conversion' => (new Query())
                        ->from(LeadUserConversion::tableName())
                        ->select([
                            'luc_user_id',
                            'count(*) as conversion_count',
                        ])
                        ->andWhere(['BETWEEN', 'DATE(luc_created_dt)', $from, $to])
                    ->groupBy(['luc_user_id'])
                ], 'conversion.luc_user_id = ps_user_id')
                ->groupBy(['user_id'])
            ], 'profit_split.user_id = users.id')
            ->andWhere(['users.status' => Employee::STATUS_ACTIVE]);

        return $query->createCommand()->queryAll();
    }
}
