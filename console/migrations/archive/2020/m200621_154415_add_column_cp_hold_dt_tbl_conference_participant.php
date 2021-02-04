<?php

use yii\db\Migration;

/**
 * Class m200621_154415_add_column_cp_hold_dt_tbl_conference_participant
 */
class m200621_154415_add_column_cp_hold_dt_tbl_conference_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference_participant}}', 'cp_hold_dt', $this->dateTime());

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference_participant}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%conference_participant}}', 'cp_hold_dt');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference_participant}}');
    }
}
