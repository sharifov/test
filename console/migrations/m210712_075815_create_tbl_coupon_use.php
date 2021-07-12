<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210712_075815_create_tbl_coupon_use
 */
class m210712_075815_create_tbl_coupon_use extends Migration
{
    private $routes = [
        '/coupon-use-crud/index',
        '/coupon-use-crud/create',
        '/coupon-use-crud/view',
        '/coupon-use-crud/update',
        '/coupon-use-crud/delete',
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

        $this->createTable('{{%coupon_use}}', [
            'cu_id' => $this->primaryKey(),
            'cu_coupon_id' => $this->integer()->notNull(),
            'cu_ip' => $this->string(40),
            'cu_user_agent' => $this->string(255),
            'cu_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-coupon_use-cu_coupon_id',
            '{{%coupon_use}}',
            ['cu_coupon_id'],
            '{{%coupon}}',
            ['c_id'],
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
        $this->dropForeignKey('FK-coupon_use-cu_coupon_id', '{{%coupon_use}}');
        $this->dropTable('{{%coupon_use}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
