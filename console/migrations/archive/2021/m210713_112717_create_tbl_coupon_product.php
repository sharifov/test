<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210713_112717_create_tbl_coupon_product
 */
class m210713_112717_create_tbl_coupon_product extends Migration
{
    private $routes = [
        '/coupon-product-crud/index',
        '/coupon-product-crud/create',
        '/coupon-product-crud/view',
        '/coupon-product-crud/update',
        '/coupon-product-crud/delete',
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

        $this->createTable('{{%coupon_product}}', [
            'cup_coupon_id' => $this->integer()->notNull(),
            'cup_product_type_id' => $this->integer()->notNull(),
            'cup_data_json' => $this->json()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-coupon_product', '{{%coupon_product}}', ['cup_coupon_id', 'cup_product_type_id']);

        $this->addForeignKey(
            'FK-coupon_product-cu_coupon_id',
            '{{%coupon_product}}',
            ['cup_coupon_id'],
            '{{%coupon}}',
            ['c_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-coupon_product-cup_product_type_id',
            '{{%coupon_product}}',
            ['cup_product_type_id'],
            '{{%product_type}}',
            ['pt_id'],
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
        $this->dropForeignKey('FK-coupon_product-cu_coupon_id', '{{%coupon_product}}');
        $this->dropForeignKey('FK-coupon_product-cup_product_type_id', '{{%coupon_product}}');
        $this->dropTable('{{%coupon_product}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
