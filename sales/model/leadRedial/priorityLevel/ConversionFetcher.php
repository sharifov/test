<?php

namespace sales\model\leadRedial\priorityLevel;

use common\models\Employee;
use common\models\Lead;
use common\models\ProfitSplit;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\VarDumper;

class ConversionFetcher
{
    public function fetch(\DateTimeImmutable $fromDt, \DateTimeImmutable $toDt): array
    {
        $from = $fromDt->format('Y-m-d 00:00:00');
        $to = $toDt->format('Y-m-d 23:59:59');

        $query = new Query();

        $query->select([
            'sum(if ((owner_share IS NULL), 100, owner_share)) as share',
            'employee_id as user_id'
        ]);
        $query->from(Lead::tableName());
        $query->leftJoin([
            'sp' => (new Query())->select(['id', '(100 - sum(ps_percent)) as owner_share'])
                ->from(Lead::tableName())
                ->innerJoin('profit_split', 'ps_lead_id = id')
                ->where(['status' => Lead::STATUS_SOLD])
                ->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to])
                ->groupBy(['id'])
        ], 'sp.id = leads.id');
        $query->where(['status' => Lead::STATUS_SOLD]);
        $query->andWhere(['IS NOT', 'employee_id', null]);
        $query->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);
        $query->groupBy('user_id');

        $complementaryQuery = new Query();
        $complementaryQuery->select([
            'ps_percent as share',
            'ps_user_id as user_id'
        ]);
        $complementaryQuery->from(Lead::tableName());
        $complementaryQuery->innerJoin(ProfitSplit::tableName(), 'ps_lead_id = id');
        $complementaryQuery->where(['status' => Lead::STATUS_SOLD]);
        $complementaryQuery->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);
        $query->union($complementaryQuery, true);

        $result = (new Query())
            ->select([
                'users.id as user_id',
                'conversion_percent' => new Expression('if ((conversion_count is null or conversion_count = 0 or share is null or share = 0), 0, round((share / conversion_count), 2))')
            ])
            ->from(['users' => Employee::tableName()])
            ->leftJoin([
                'share_query' => (new Query())
                    ->select(['sum(union_query.share) as share', 'union_query.user_id as share_user_id'])
                    ->from(['union_query' => $query])
                    ->groupBy(['share_user_id'])
            ], 'share_query.share_user_id = users.id')
            ->leftJoin([
                    'conversion' => (new Query())
                        ->from(LeadUserConversion::tableName())
                        ->select([
                            'luc_user_id',
                            'count(*) as conversion_count',
                        ])
                        ->andWhere(['BETWEEN', 'DATE(luc_created_dt)', $from, $to])
                    ->groupBy(['luc_user_id'])
                ], 'conversion.luc_user_id = users.id')
            ->andWhere(['status' => Employee::STATUS_ACTIVE]);

//        VarDumper::dump($result->createCommand()->getRawSql());die;

        return $result->createCommand()->queryAll();
    }
//    public function fetch(\DateTimeImmutable $fromDt, \DateTimeImmutable $toDt): array
//    {
//        $from = $fromDt->format('Y-m-d H:i:s');
//        $to = $toDt->format('Y-m-d H:i:s');
//
//        $query = (new Query())
//            ->select([
//                'users.id as user_id',
//                'conversion_percent' => new Expression('if ((conversion_cnt is null or conversion_cnt = 0 or share is null or share = 0), 0, (share / conversion_cnt))')
//            ])
//            ->from(['users' => Employee::tableName()])
//            ->leftJoin(['profit_split' => (new Query())
//                ->select([
//                    'ps_user_id as user_id',
//                    'sum(ps_percent) as share',
//                    'if (conversion_count is null, 0, conversion_count) as conversion_cnt',
//                ])
//                ->from(ProfitSplit::tableName())
//                ->innerJoin(
//                    ['l' => Lead::tableName()],
//                    'l.id = ps_lead_id AND l.status = :status AND (DATE(l.l_status_dt) BETWEEN :from AND :to)',
//                    [':status' => Lead::STATUS_SOLD, ':from' => $from, ':to' => $to]
//                )
//                ->leftJoin([
//                    'conversion' => (new Query())
//                        ->from(LeadUserConversion::tableName())
//                        ->select([
//                            'luc_user_id',
//                            'count(*) as conversion_count',
//                        ])
//                        ->andWhere(['BETWEEN', 'DATE(luc_created_dt)', $from, $to])
//                    ->groupBy(['luc_user_id'])
//                ], 'conversion.luc_user_id = ps_user_id')
//                ->groupBy(['user_id'])
//            ], 'profit_split.user_id = users.id')
//            ->andWhere(['users.status' => Employee::STATUS_ACTIVE]);
//
//        return $query->createCommand()->queryAll();
//    }
}
