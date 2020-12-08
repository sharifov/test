<?php

use yii\db\Migration;

/**
 * Class m201127_131444_add_column_to_client_account
 */
class m201127_131444_add_column_to_client_account extends Migration
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
    }
}
