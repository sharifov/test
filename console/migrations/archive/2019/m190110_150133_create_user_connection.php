<?php

use yii\db\Migration;

/**
 * Class m190110_150133_create_user_connection
 */
class m190110_150133_create_user_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_connection}}',	[
            'uc_id'                     => $this->primaryKey(),
            'uc_connection_id'          => $this->integer()->notNull(),
            'uc_user_id'                => $this->integer(),
            'uc_lead_id'                => $this->integer(),
            'uc_user_agent'             => $this->string(255),
            'uc_controller_id'          => $this->string(50),
            'uc_action_id'              => $this->string(50),
            'uc_page_url'               => $this->string(500),
            'uc_ip'                     => $this->string(40),
            'uc_created_dt'             => $this->dateTime(),
        ], $tableOptions);

        //$this->addPrimaryKey('PK-user_connection_uc_id', '{{%user_connection}}', ['uc_id']);

        $this->createIndex('IND-user_connection_uc_connection_id', '{{%user_connection}}', ['uc_connection_id']);

        $this->addForeignKey('FK-user_connection_uc_user_id', '{{%user_connection}}', ['uc_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-user_connection_uc_lead_id', '{{%user_connection}}', ['uc_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_connection}}');
    }


}
