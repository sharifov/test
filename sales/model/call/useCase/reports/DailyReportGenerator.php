<?php

namespace sales\model\call\useCase\reports;

use common\models\Call;
use yii\db\Query;

class DailyReportGenerator
{
    public function generate(array $phones, string $date): array
    {
        $fromDate =  date('Y-m-d H:i:s', strtotime($date));
        $toDate =  date('Y-m-d H:i:s', strtotime('+1 day', strtotime($date)));
        $query = (new Query())
            ->select([
                '`Time Stamp (UTC)`' => 'c_created_dt',
                '`Call ID`' => 'c_id',
                '`Call Length`',
                '`Phone number`' => 'c_to',
            ])
            ->from(Call::tableName())
            ->leftJoin(
                [
                    'ss' => (new Query())
                        ->select([
                            'c_parent_id',
                            '`Call Length`' => 'sum(c_recording_duration)'
                        ])
                        ->from(Call::tableName())
                        ->groupBy('c_parent_id')
                ],
                Call::tableName() . '.c_id = ss.c_parent_id'
            )
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_to' => $phones])
            ->andWhere(['>=', 'c_created_dt', $fromDate])
            ->andWhere(['<', 'c_created_dt', $toDate])
            ->orderBy(['`Time Stamp (UTC)`' => SORT_ASC]);
        $data = $query->all();
        array_unshift($data, ['Time Stamp (UTC)', 'Call ID', 'Call Length', 'Phone number']);
        return $data;
    }
}
