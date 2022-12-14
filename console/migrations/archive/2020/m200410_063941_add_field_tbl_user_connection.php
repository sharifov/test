<?php

use yii\db\Migration;

/**
 * Class m200410_063941_add_field_tbl_user_connection
 */
class m200410_063941_add_field_tbl_user_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_connection}}', 'uc_connection_uid', $this->string(30)->unique());
        $this->addColumn('{{%user_connection}}', 'uc_app_instance', $this->string(20));
        $this->addColumn('{{%user_connection}}', 'uc_sub_list', $this->string(255));

        $this->createIndex('IND-user_connection-uc_connection_uid', '{{%user_connection}}', ['uc_connection_uid'], true);
        $this->createIndex('IND-user_connection-uc_app_instance', '{{%user_connection}}', ['uc_app_instance']);
        $this->alterColumn('{{%user_connection}}', 'uc_id', $this->bigInteger()->notNull() . ' AUTO_INCREMENT');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-user_connection-uc_connection_uid', '{{%user_connection}}');
        $this->dropColumn('{{%user_connection}}', 'uc_connection_uid');
        $this->dropColumn('{{%user_connection}}', 'uc_sub_list');
        $this->dropColumn('{{%user_connection}}', 'uc_app_instance');
        $this->alterColumn('{{%user_connection}}', 'uc_id', $this->integer()->notNull() . ' AUTO_INCREMENT');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
