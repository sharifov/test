<?php

namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m210610_032806_create_product_quote_label_tables
 */
class m210610_032806_create_product_quote_label_tables extends Migration
{
    private $routes = [
        '/flight/flight-quote-label-crud/index',
        '/flight/flight-quote-label-crud/create',
        '/flight/flight-quote-label-crud/view',
        '/flight/flight-quote-label-crud/update',
        '/flight/flight-quote-label-crud/delete',
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

        $this->createTable('{{%flight_quote_label}}', [
            'fql_quote_id' => $this->integer()->notNull(),
            'fql_label_key' => $this->string(50)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-flight_quote_label', '{{%flight_quote_label}}', ['fql_quote_id', 'fql_label_key']);

        $this->addForeignKey(
            'FK-flight_quote_label-quote_id',
            '{{%flight_quote_label}}',
            'fql_quote_id',
            '{{%flight_quote}}',
            'fq_id',
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
        $this->dropTable('{{%flight_quote_label}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
