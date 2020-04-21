<?php

use yii\db\Migration;

/**
 * Class m191122_095909_add_column_client_id_table_sms
 */
class m191122_095909_add_column_client_id_table_sms extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sms}}', 's_client_id', $this->integer());
        $this->addForeignKey('FK-sms_s_client_id', '{{%sms}}', ['s_client_id'], '{{%clients}}', ['id'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sms}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-sms_s_client_id', '{{%sms}}');
        $this->dropColumn('{{%sms}}', 's_client_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sms}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
