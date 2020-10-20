<?php

use yii\db\Migration;

/**
 * Class m201020_194648_drop_tbl_client_chat_request
 */
class m201020_194648_drop_tbl_client_chat_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-cch_ccr_id', '{{%client_chat}}');
        $this->dropTable('{{%client_chat_request}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%client_chat_request}}', [
            'ccr_id' => $this->primaryKey(),
            'ccr_event' => $this->tinyInteger(2),
            'ccr_rid' => $this->string(150),
            'ccr_json_data' => $this->text(),
            'ccr_created_dt' => $this->dateTime()
        ], $tableOptions);
        $this->createIndex('IND-ccr_event', '{{%client_chat_request}}', ['ccr_event']);
        $this->createIndex('IND-ccr_rid', '{{%client_chat_request}}', ['ccr_rid']);
        $this->addForeignKey('FK-cch_ccr_id', '{{%client_chat}}', ['cch_ccr_id'], '{{%client_chat_request}}', ['ccr_id'], 'SET NULL', 'CASCADE');
    }
}
