<?php

use yii\db\Migration;

/**
 * Class m200229_101351_add_column_cs_deadline_dt_tbl_cases
 */
class m200229_101351_add_column_cs_deadline_dt_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_deadline_dt', $this->dateTime()->null());

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
        $this->dropColumn('{{%cases}}', 'cs_deadline_dt');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
