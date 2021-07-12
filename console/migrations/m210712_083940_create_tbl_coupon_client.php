<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210712_083940_create_tbl_coupon_client
 */
class m210712_083940_create_tbl_coupon_client extends Migration
{
    private $routes = [
        '/coupon-client-crud/index',
        '/coupon-client-crud/create',
        '/coupon-client-crud/view',
        '/coupon-client-crud/update',
        '/coupon-client-crud/delete',
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

        $this->createTable('{{%coupon_client}}', [
            'cuc_id' => $this->primaryKey(),
            'cuc_coupon_id' => $this->integer()->notNull(),
            'cuc_client_id' => $this->integer()->notNull(),
            'cuc_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-coupon_client-cuc_coupon_id',
            '{{%coupon_client}}',
            ['cuc_coupon_id'],
            '{{%coupon}}',
            ['c_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_client-cuc_client_id',
            '{{%coupon_client}}',
            ['cuc_client_id'],
            '{{%clients}}',
            ['id'],
            'CASCADE',
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
        $this->dropForeignKey('FK-coupon_client-cuc_coupon_id', '{{%coupon_client}}');
        $this->dropForeignKey('FK-coupon_client-cuc_client_id', '{{%coupon_client}}');
        $this->dropTable('{{%coupon_client}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
