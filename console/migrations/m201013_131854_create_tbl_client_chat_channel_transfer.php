<?php

use yii\db\Migration;

/**
 * Class m201013_131854_create_tbl_client_chat_channel_transfer
 */
class m201013_131854_create_tbl_client_chat_channel_transfer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_chat_channel_transfer}}', [
            'cctr_from_ccc_id' => $this->integer(),
            'cctr_to_ccc_id' => $this->integer(),
            'cctr_created_user_id' => $this->integer(),
            'cctr_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-chat_channel_transfer', '{{%client_chat_channel_transfer}}', ['cctr_from_ccc_id', 'cctr_to_ccc_id']);

        $this->addForeignKey(
            'FK-chat_channel_transfer-cctr_from_ccc_id',
            '{{%client_chat_channel_transfer}}',
            ['cctr_from_ccc_id'],
            '{{%client_chat_channel}}',
            'ccc_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-chat_channel_transfer-cctr_to_ccc_id',
            '{{%client_chat_channel_transfer}}',
            ['cctr_to_ccc_id'],
            '{{%client_chat_channel}}',
            'ccc_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-chat_channel_transfer-cctr_created_user_id',
            '{{%client_chat_channel_transfer}}',
            ['cctr_created_user_id'],
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_channel_transfer}}');
    }
}
