<?php

namespace sales\model\leadRedial\priorityLevel;

use common\models\Lead;
use yii\db\Query;

class ConversionFetcher
{
    public function fetch(int $userId, \DateTimeImmutable $fromDt)
    {
        $from = $fromDt->format('Y-m-d H:i:s');
        $to = date('Y-m-d H:i:s');

        $query = (new Query())
            ->select([
                Lead::tableName() . '.id',
                Lead::tableName() . '.gid',
                '(ROUND(if(sp.`owner_share` is null, 1, sp.`owner_share`) * (final_profit - agents_processing_fee), 2)) as gross_profit',
                'if(sp.`owner_share` is null, 1, ROUND(sp.owner_share, 2)) as share',
                'l_status_dt',
                'created'
            ])
            ->from(Lead::tableName())
            ->leftJoin([
            'sp' => (new Query())->select(['id', '(100 - sum(ps_percent)) / 100 as owner_share'])
                ->from(Lead::tableName())
                ->innerJoin('profit_split', 'ps_lead_id = id')
                ->where(['status' => Lead::STATUS_SOLD])
                ->andWhere([
                    'OR',
                    ['IS', 'l_status_dt', null],
                    ['BETWEEN', 'DATE(l_status_dt)', $from, $to]
                ])
                ->andWhere(['employee_id' => $userId])
                ->groupBy(['id'])
            ], 'sp.id = leads.id')
            ->andWhere(['status' => Lead::STATUS_SOLD])
            ->andWhere([
                'OR',
                ['IS', 'l_status_dt', null],
                ['BETWEEN', 'DATE(l_status_dt)', $from, $to]
            ])
            ->andWhere(['employee_id' => $userId]);


        return $query->createCommand()->getRawSql();

        $complementaryQuery = new Query();
        $complementaryQuery->select([
            'id',
            'gid',
            '(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2)) as gross_profit',
            'ROUND((ps_percent / 100), 2) as share',
            'l_status_dt',
            'created'
        ]);
        $complementaryQuery->from(Lead::tableName());
        $complementaryQuery->innerJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . $userId);
        $complementaryQuery->where(['status' => Lead::STATUS_SOLD]);
        $complementaryQuery->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);

        $query->union($complementaryQuery, true);

        return $query->createCommand()->getRawSql();
    }
}
