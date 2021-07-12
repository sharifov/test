<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210712_101532_create_tbl_coupon_send
 */
class m210712_101532_create_tbl_coupon_send extends Migration
{
    private $routes = [
        '/coupon-send-crud/index',
        '/coupon-send-crud/create',
        '/coupon-send-crud/view',
        '/coupon-send-crud/update',
        '/coupon-send-crud/delete',
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

        $this->createTable('{{%coupon_send}}', [
            'cus_id' => $this->primaryKey(),
            'cus_coupon_id' => $this->integer()->notNull(),
            'cus_user_id' => $this->integer(),
            'cus_type_id' => $this->tinyInteger()->notNull()->defaultValue(1), // email
            'cus_send_to' => $this->string(50)->notNull(),
            'cus_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-coupon_send-cus_coupon_id',
            '{{%coupon_send}}',
            ['cus_coupon_id'],
            '{{%coupon}}',
            ['c_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_send-cus_user_id',
            '{{%coupon_send}}',
            ['cus_user_id'],
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
        $this->dropForeignKey('FK-coupon_send-cus_coupon_id', '{{%coupon_send}}');
        $this->dropForeignKey('FK-coupon_send-cus_user_id', '{{%coupon_send}}');
        $this->dropTable('{{%coupon_send}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
