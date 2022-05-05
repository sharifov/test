<?php

use yii\db\Migration;

/**
 * Class m220429_061940_add_column_tbl_shift_schedule_type
 */
class m220429_061940_add_column_tbl_shift_schedule_type extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%shift_schedule_type}}', 'sst_subtype_id', $this->smallInteger());
        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }


    public function safeDown()
    {
        $this->dropColumn('{{%shift_schedule_type}}', 'sst_subtype_id');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%shift_schedule_type}}');
    }
}
