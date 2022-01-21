<?php

use common\models\Lead;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m211221_130207_migrate_sold_lead_owners_in_tips_split_tbl
 */
class m211221_130207_migrate_sold_lead_owners_in_tips_split_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $subQuery = (new \yii\db\Query())
            ->select(new Expression('leads.id, leads.tips, leads.employee_id'))
            ->from('{{%leads}}')
            ->leftJoin('{{%tips_split}}', 'ts_lead_id = id and ts_user_id = employee_id')
            ->where(['status' => Lead::STATUS_SOLD, 'ts_user_id' => null])
            ->andWhere(['>', 'tips', 0]);

        $rows = (new \yii\db\Query())
            ->select(['lead_id' => 'a.id', 'a.tips', 'a.employee_id', new Expression('sum(ts_percent) as `total_percent`')])
            ->from('{{%tips_split}}')
            ->join('right join', ['a' => $subQuery], new Expression('a.id = tips_split.ts_lead_id'))
            ->groupBy('lead_id, a.tips, a.employee_id')
            ->andWhere(new Expression('a.employee_id is not null'))
            ->having('total_percent < 100 or total_percent is null')
            ->all();

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'ts_lead_id' => $row['lead_id'],
                'ts_user_id' => $row['employee_id'],
                'ts_percent' => 100 - (int)$row['total_percent'],
                'ts_updated_dt' => date('Y-m-d H:i:s')
            ];
        }
        Yii::$app->db->createCommand()->batchInsert('{{%tips_split}}', ['ts_lead_id', 'ts_user_id', 'ts_percent', 'ts_updated_dt'], $data)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
