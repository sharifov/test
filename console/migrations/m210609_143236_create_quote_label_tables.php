<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210609_143236_create_quote_label_tables
 */
class m210609_143236_create_quote_label_tables extends Migration
{
    private $routes = [
        '/quote-label-crud/index',
        '/quote-label-crud/create',
        '/quote-label-crud/view',
        '/quote-label-crud/update',
        '/quote-label-crud/delete',
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

        $this->createTable('{{%quote_label}}', [
            'ql_quote_id' => $this->integer()->notNull(),
            'ql_label_key' => $this->string(50)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-quote_label', '{{%quote_label}}', ['ql_quote_id', 'ql_label_key']);

        $this->addForeignKey(
            'FK-quote_label-quote_id',
            '{{%quote_label}}',
            ['ql_quote_id'],
            '{{%quotes}}',
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
        $this->dropTable('{{%quote_label}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
