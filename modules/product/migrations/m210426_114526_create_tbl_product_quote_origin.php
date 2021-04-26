<?php

namespace modules\product\migrations;

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210426_114526_create_tbl_product_quote_origin
 */
class m210426_114526_create_tbl_product_quote_origin extends Migration
{
    private string $tableName = '{{%product_quote_origin}}';

    private array $route = [
        '/product/product-quote-origin/create',
        '/product/product-quote-origin/update',
        '/product/product-quote-origin/delete',
        '/product/product-quote-origin/view',
        '/product/product-quote-origin/index',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
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

        $this->createTable($this->tableName, [
            'pqa_product_id' => $this->integer(),
            'pqa_quote_id' => $this->integer()
        ], $tableOptions);

        $this->addPrimaryKey('PK-product_quote_origin', $this->tableName, ['pqa_product_id', 'pqa_quote_id']);
        $this->addForeignKey('FK-product_quote_origin-pqa_product_id', $this->tableName, 'pqa_product_id', '{{%product}}', 'pr_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote_origin-pqa_quote_id', $this->tableName, 'pqa_quote_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_origin-pqa_product_id', $this->tableName);
        $this->dropForeignKey('FK-product_quote_origin-pqa_quote_id', $this->tableName);
        $this->dropTable($this->tableName);
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
