<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210423_113810_create_tbl_file_product_quote
 */
class m210423_113810_create_tbl_file_product_quote extends Migration
{
    private $routes = [
        '/file-storage/file-product-quote/index',
        '/file-storage/file-product-quote/update',
        '/file-storage/file-product-quote/create',
        '/file-storage/file-product-quote/delete',
        '/file-storage/file-product-quote/view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file_product_quote}}', [
            'fpq_fs_id' => $this->integer(),
            'fpq_pq_id' => $this->integer(),
            'fpq_created_dt' => $this->dateTime(),
        ]);

        $this->addPrimaryKey('PK-file_product_quote-fpq_fs_id-fpq_pq_id', '{{%file_product_quote}}', ['fpq_fs_id', 'fpq_pq_id']);
        $this->addForeignKey(
            'FK-file_product_quote-fpq_fs_id-fpq_pq_id',
            '{{%file_product_quote}}',
            ['fpq_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('IND-file_product_quote-fpq_pq_id', '{{%file_product_quote}}', ['fpq_pq_id']);

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-file_product_quote-fpq_pq_id', '{{%file_product_quote}}');
        $this->dropForeignKey('FK-file_product_quote-fpq_fs_id-fpq_pq_id', '{{%file_product_quote}}');

        $this->dropTable('{{%file_product_quote}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
