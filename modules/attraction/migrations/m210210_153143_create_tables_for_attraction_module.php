<?php

namespace modules\attraction\migrations;

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m210210_153143_create_tables_for_attraction_module
 */
class m210210_153143_create_tables_for_attraction_module extends Migration
{
    public $routes = [
        '/attraction/default/*',
    ];

    public $roles = [
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

        $this->createTable('{{%attraction}}', [
            'atn_id'          => $this->primaryKey(),
            'atn_product_id'  => $this->integer(),
            'atn_date_from'   => $this->date(),
            'atn_date_to'     => $this->date(),
            'atn_destination' => $this->string(100)
        ], $tableOptions);

        $this->addForeignKey('FK-attraction-atn_product_id', '{{%attraction}}', ['atn_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%attraction}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
