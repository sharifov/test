<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210719_075520_create_tbl_coupon_user_action
 */
class m210719_075520_create_tbl_coupon_user_action extends Migration
{
    private $routes = [
        '/coupon-user-action-crud/index',
        '/coupon-user-action-crud/create',
        '/coupon-user-action-crud/view',
        '/coupon-user-action-crud/update',
        '/coupon-user-action-crud/delete',
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

        $this->createTable('{{%coupon_user_action}}', [
            'cuu_id' => $this->primaryKey(),
            'cuu_coupon_id' => $this->integer()->notNull(),
            'cuu_action_id' => $this->integer()->notNull(),
            'cuu_api_user_id' => $this->integer(),
            'cuu_user_id' => $this->integer(),
            'cuu_description' => $this->string(255),
            'cuu_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-coupon_user_action-cuu_coupon_id',
            '{{%coupon_user_action}}',
            ['cuu_coupon_id'],
            '{{%coupon}}',
            ['c_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_user_action-cuu_api_user_id',
            '{{%coupon_user_action}}',
            ['cuu_api_user_id'],
            '{{%api_user}}',
            ['au_id'],
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_user_action-cuu_user_id',
            '{{%coupon_user_action}}',
            ['cuu_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-coupon_user_action-cuu_coupon_id', '{{%coupon_user_action}}');
        $this->dropForeignKey('FK-coupon_user_action-cuu_api_user_id', '{{%coupon_user_action}}');
        $this->dropForeignKey('FK-coupon_user_action-cuu_user_id', '{{%coupon_user_action}}');
        $this->dropTable('{{%coupon_user_action}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
