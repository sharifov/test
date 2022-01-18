<?php

use yii\db\Migration;

/**
 * Class m210830_081937_create_tbl_client_chat_data_request
 *
 * @property string $tableName
 */
class m210830_081937_create_tbl_client_chat_data_request extends Migration
{
    private string $tableName = '{{%client_chat_data_request}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'ccdr_id' => $this->primaryKey(),
            'ccdr_chat_id' => $this->integer()->notNull(),
            'ccdr_data_json' => $this->json(),
            'ccdr_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-client_chat_data_request-ccdr_chat_id', $this->tableName, 'ccdr_chat_id', '{{%client_chat}}', 'cch_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_data_request-ccdr_chat_id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
