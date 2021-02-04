<?php

use yii\db\Migration;

/**
 * Class m200601_061219_alter_tbl_email_add_column_client_id
 */
class m200601_061219_alter_tbl_email_add_column_client_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email}}', 'e_client_id', $this->integer());
        $this->addForeignKey('FK-email-e_client_id', '{{%email}}', ['e_client_id'], '{{%clients}}', ['id']);
        $this->createIndex('IND-email-e_client_id', '{{%email}}', ['e_client_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-email-e_client_id', '{{%email}}');
        $this->dropIndex('IND-email-e_client_id', '{{%email}}');
        $this->dropColumn('{{%email}}', 'e_client_id');
    }
}
