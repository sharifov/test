<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200917_070826_create_tbl_client_chat_channel_translate
 */
class m200917_070826_create_tbl_client_chat_channel_translate extends Migration
{
    public $route = [
        '/client-chat-channel-translate/*',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables

        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        // Alter the encoding of each table

        $db->createCommand("ALTER TABLE `language` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")->execute();
        //$db->createCommand("ALTER TABLE `language` MODIFY `language_id` VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();




        $this->createTable('{{%client_chat_channel_translate}}', [
            'ct_channel_id'     => $this->integer()->notNull(),
            'ct_language_id'    => $this->string(5)->notNull(),
            'ct_name'           => $this->string(100)->notNull(),
            'ct_created_user_id' => $this->integer(),
            'ct_updated_user_id' => $this->integer(),
            'ct_created_dt' => $this->dateTime(),
            'ct_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_channel_translate', '{{%client_chat_channel_translate}}', ['ct_channel_id', 'ct_language_id']);
        $this->addForeignKey('FK-client_chat_channel_translate-ct_channel_id', '{{%client_chat_channel_translate}}', ['ct_channel_id'], '{{%client_chat_channel}}', ['ccc_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_channel_translate-ct_language_id', '{{%client_chat_channel_translate}}', ['ct_language_id'], '{{%language}}', ['language_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_channel_translate-ct_created_user_id', '{{%client_chat_channel_translate}}', ['ct_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-client_chat_channel_translate-ct_updated_user_id', '{{%client_chat_channel_translate}}', ['ct_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_channel_translate}}');
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
