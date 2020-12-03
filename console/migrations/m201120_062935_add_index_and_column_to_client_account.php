<?php

use yii\db\Migration;

/**
 * Class m201120_062935_add_index_and_column_to_client_account
 */
class m201120_062935_add_index_and_column_to_client_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('client_account');
        if (!isset($table->columns['ca_email'])) {
            $this->addColumn('{{%client_account}}', 'ca_email', $this->string(100));
        }
        $this->createIndex('IND-client_account-ca_username', '{{%client_account}}', 'ca_username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->schema->getTableSchema('client_account');
        if (isset($table->columns['ca_email'])) {
            $this->dropColumn('{{%client_account}}', 'ca_email');
        }
        $this->dropIndex('IND-client_account-ca_username', '{{%client_account}}');
    }
}
