<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200315_082836_sms_distribution_list
 */
class m200315_082836_sms_distribution_list extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/sms-distribution-list/*',
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

        $this->createTable('{{%sms_distribution_list}}', [
            'sdl_id' => $this->primaryKey(),
            'sdl_com_id' => $this->integer(),
            'sdl_project_id' => $this->integer(),
            'sdl_phone_from' => $this->string(20)->notNull(),
            'sdl_phone_to' => $this->string(20)->notNull(),
            'sdl_client_id' => $this->integer(),
            'sdl_text' => $this->text()->notNull(),
            'sdl_start_dt' => $this->dateTime(),
            'sdl_end_dt' => $this->dateTime(),
            'sdl_status_id' => $this->tinyInteger(),
            'sdl_priority' => $this->smallInteger()->defaultValue(0),
            'sdl_error_message' => $this->text(),
            'sdl_message_sid' => $this->string(40),
            'sdl_num_segments'   => $this->smallInteger(),
            'sdl_price'         => $this->decimal(5, 2),
            'sdl_created_user_id'   => $this->integer(),
            'sdl_updated_user_id'   => $this->integer(),
            'sdl_created_dt' => $this->dateTime(),
            'sdl_updated_dt' => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-sms_distribution_list-sdl_client_id',
            '{{%sms_distribution_list}}',
            'sdl_client_id',
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-sms_distribution_list-sdl_project_id',
            '{{%sms_distribution_list}}',
            'sdl_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'SET NULL'
        );

        $this->addForeignKey(
            'FK-sms_distribution_list-sdl_created_user_id',
            '{{%sms_distribution_list}}',
            'sdl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-sms_distribution_list-sdl_updated_user_id',
            '{{%sms_distribution_list}}',
            'sdl_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-sms_distribution_list-sdl_status_id', '{{%sms_distribution_list}}', ['sdl_status_id']);
        $this->createIndex('IND-sms_distribution_list-sdl_phone_from', '{{%sms_distribution_list}}', ['sdl_phone_from']);
        $this->createIndex('IND-sms_distribution_list-sdl_phone_to', '{{%sms_distribution_list}}', ['sdl_phone_to']);
        $this->createIndex('IND-sms_distribution_list-sdl_created_dt', '{{%sms_distribution_list}}', ['sdl_created_dt']);
        $this->createIndex('IND-sms_distribution_list-sdl_priority', '{{%sms_distribution_list}}', ['sdl_priority']);
        $this->createIndex('IND-sms_distribution_list-sdl_start_dt', '{{%sms_distribution_list}}', ['sdl_start_dt']);
        $this->createIndex('IND-sms_distribution_list-sdl_end_dt', '{{%sms_distribution_list}}', ['sdl_end_dt']);


        $this->insert('{{%setting}}', [
            's_key' => 'sms_distribution_count',
            's_name' => 'SMS distribution count',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);


        (new RbacMigrationService())->up($this->routes, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_distribution_list}}');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'sms_distribution_count',
        ]]);

        (new RbacMigrationService())->down($this->routes, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
