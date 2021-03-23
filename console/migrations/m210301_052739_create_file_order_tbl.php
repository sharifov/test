<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210301_052739_create_file_order_tbl
 */
class m210301_052739_create_file_order_tbl extends Migration
{
    private $routes = [
        '/file-storage/file-order/*',
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
        $this->createTable('{{%file_order}}', [
            'fo_id' => $this->primaryKey(),
            'fo_fs_id' => $this->integer(),
            'fo_or_id' => $this->integer(),
            'fo_pq_id' => $this->integer(),
            'fo_category_id' => $this->integer(),
            'fo_created_dt' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'FK-file_client-fo_fs_id',
            '{{%file_order}}',
            ['fo_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('IND-file_order-fo_or_id', '{{%file_order}}', ['fo_or_id']);
        $this->createIndex('IND-file_order-fo_pq_id', '{{%file_order}}', ['fo_pq_id']);
        $this->createIndex('IND-file_order-fo_category_id', '{{%file_order}}', ['fo_category_id']);

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file_order}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
