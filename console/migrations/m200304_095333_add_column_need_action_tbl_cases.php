<?php

use yii\db\Migration;

/**
 * Class m200304_095333_add_column_need_action_tbl_cases
 */
class m200304_095333_add_column_need_action_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_need_action', $this->boolean()->defaultValue(false)->null());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cases}}', 'cs_need_action');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
