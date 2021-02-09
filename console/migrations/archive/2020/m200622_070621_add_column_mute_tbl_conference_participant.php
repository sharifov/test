<?php

use yii\db\Migration;

/**
 * Class m200622_070621_add_column_mute_tbl_conference_participant
 */
class m200622_070621_add_column_mute_tbl_conference_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference_participant}}', 'cp_mute', $this->boolean()->defaultValue(false));

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
        $this->dropColumn('{{%conference_participant}}', 'cp_mute');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference_participant}}');
    }
}
