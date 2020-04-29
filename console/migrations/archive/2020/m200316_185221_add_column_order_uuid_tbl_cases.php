<?php

use yii\db\Migration;

/**
 * Class m200316_185221_add_column_order_uuid_tbl_cases
 */
class m200316_185221_add_column_order_uuid_tbl_cases extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_order_uid', $this->string(7)->null());

        $this->afterRun();
    }

    public function safeDown()
    {
        $this->dropColumn('{{%cases}}', 'cs_order_uid');

        $this->afterRun();
    }

    private function afterRun(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
