<?php

namespace modules\product\migrations;

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210401_053411_create_product_quote_relation_tbl
 */
class m210401_053411_create_product_quote_relation_tbl extends Migration
{
    private $routes = [
        '/product/product-quote-relation-crud/index',
        '/product/product-quote-relation-crud/view',
        '/product/product-quote-relation-crud/create',
        '/product/product-quote-relation-crud/update',
        '/product/product-quote-relation-crud/delete',
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

        $this->createTable('{{%product_quote_relation}}', [
            'pqr_parent_pq_id' => $this->integer()->notNull(),
            'pqr_related_pq_id' => $this->integer()->notNull(),
            'pqr_type_id' => $this->tinyInteger()->notNull()->comment('1 - replace, 2 - clone'),
            'pqr_created_user_id' => $this->integer(),
            'pqr_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-product_quote_relation', '{{%product_quote_relation}}', ['pqr_parent_pq_id', 'pqr_related_pq_id', 'pqr_type_id']);

        $this->addForeignKey(
            'FK-product_quote_relation-pqr_parent_pq_id',
            '{{%product_quote_relation}}',
            ['pqr_parent_pq_id'],
            '{{%product_quote}}',
            ['pq_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-product_quote_relation-pqr_related_pq_id',
            '{{%product_quote_relation}}',
            ['pqr_related_pq_id'],
            '{{%product_quote}}',
            ['pq_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-product_quote_relation-pqr_created_user_id',
            '{{%product_quote_relation}}',
            ['pqr_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_quote_relation}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
