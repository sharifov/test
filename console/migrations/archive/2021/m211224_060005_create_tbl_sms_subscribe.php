<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211224_060005_create_tbl_sms_subscribe
 */
class m211224_060005_create_tbl_sms_subscribe extends Migration
{
    private $routes = [
        '/sms-subscribe-crud/view',
        '/sms-subscribe-crud/index',
        '/sms-subscribe-crud/create',
        '/sms-subscribe-crud/update',
        '/sms-subscribe-crud/delete',
    ];

    private $roles = [
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

        $this->createTable('{{%sms_subscribe}}', [
            'ss_id' => $this->primaryKey(),
            'ss_cpl_id' => $this->integer(),
            'ss_project_id' => $this->integer(),
            'ss_status_id' => $this->integer()->notNull(),
            'ss_created_dt' => $this->dateTime(),
            'ss_updated_dt' => $this->dateTime(),
            'ss_deadline_dt' => $this->dateTime(),
            'ss_created_user_id' => $this->integer(),
            'ss_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-sms_subscribe-ss_cpl_id',
            '{{%sms_subscribe}}',
            'ss_cpl_id',
            '{{%contact_phone_list}}',
            'cpl_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-sms_subscribe-ss_project_id',
            '{{%sms_subscribe}}',
            'ss_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-sms_subscribe-ss_created_user_id',
            '{{%sms_subscribe}}',
            ['ss_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-sms_subscribe-ss_updated_user_id',
            '{{%sms_subscribe}}',
            ['ss_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-sms_subscribe-ss_updated_user_id', '{{%sms_subscribe}}');
        $this->dropForeignKey('FK-sms_subscribe-ss_created_user_id', '{{%sms_subscribe}}');
        $this->dropForeignKey('FK-sms_subscribe-ss_project_id', '{{%sms_subscribe}}');
        $this->dropForeignKey('FK-sms_subscribe-ss_cpl_id', '{{%sms_subscribe}}');

        $this->dropTable('{{%sms_subscribe}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
