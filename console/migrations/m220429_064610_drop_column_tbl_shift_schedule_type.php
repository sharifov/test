<?php

use yii\db\Migration;

/**
 * Class m220429_064610_drop_column_tbl_shift_schedule_type
 */
class m220429_064610_drop_column_tbl_shift_schedule_type extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%shift_schedule_type}}', 'sst_work_time');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }


    public function safeDown()
    {
        $this->addColumn('{{%shift_schedule_type}}', 'sst_work_time', $this->smallInteger());
        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }
}
