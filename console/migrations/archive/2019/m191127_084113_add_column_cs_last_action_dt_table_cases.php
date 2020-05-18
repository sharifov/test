<?php

use yii\db\Migration;

/**
 * Class m191127_084113_add_column_cs_last_action_dt_table_cases
 */
class m191127_084113_add_column_cs_last_action_dt_table_cases extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_last_action_dt', $this->dateTime());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cases}}', 'cs_last_action_dt');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
