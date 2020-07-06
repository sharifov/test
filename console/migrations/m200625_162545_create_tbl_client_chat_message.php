<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\db\Migration;

/**
 * Class m200624_162545_create_tbl_client_chat_message
 */
class m200625_162545_create_tbl_client_chat_message extends Migration
{

    private $routes = [
        '/client-chat-message-crud/create',
        '/client-chat-message-crud/update',
        '/client-chat-message-crud/delete',
        '/client-chat-message-crud/view',
        '/client-chat-message-crud/index',
        '/client-chat-message-crud/download',
    ];

    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $tableOptions = 'PARTITION BY RANGE (ccm_sent_dt)';
        $this->createTable('{{%client_chat_message}}', [
            'ccm_id' => $this->bigInteger()->append('generated always as identity'),
            'ccm_rid' => $this->string(150)->notNull(),
            'ccm_client_id' => $this->integer(),
            'ccm_user_id' => $this->integer(),
            'ccm_sent_dt' => $this->dateTime()->notNull(),
            'ccm_has_attachment' => $this->tinyInteger(1)->defaultValue(0),
            'ccm_body' => 'JSONB NOT NULL'
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_message_ccm_id', '{{%client_chat_message}}', ['ccm_id', 'ccm_sent_dt']);
        $this->createIndex('ccm_client_id_idx', '{{%client_chat_message}}', 'ccm_client_id');

        $now = date_create("now");
        $dates = ClientChatMessage::partitionDatesFrom($now);
        ClientChatMessage::createMonthlyPartition($dates[0], $dates[1]);
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

//        $this->dropTable('{{%client_chat_message}}');
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
