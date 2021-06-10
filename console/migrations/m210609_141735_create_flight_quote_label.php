<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210609_141735_create_flight_quote_label
 */
class m210609_141735_create_flight_quote_label extends Migration
{
    private $routes = [
        '/flight-quote-label-list-crud/index',
        '/flight-quote-label-list-crud/create',
        '/flight-quote-label-list-crud/view',
        '/flight-quote-label-list-crud/update',
        '/flight-quote-label-list-crud/delete',
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

        $this->createTable('{{%flight_quote_label_list}}', [
            'fqll_id' => $this->primaryKey(),
            'fqll_label_key' => $this->string(50)->notNull()->unique(),
            'fqll_origin_description' => $this->string(255),
            'fqll_description' => $this->string(255),
            'fqll_created_dt' => $this->dateTime(),
            'fqll_updated_dt' => $this->dateTime(),
            'fqll_created_user_id' => $this->integer(),
            'fqll_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-flight_quote_label-created_user_id',
            '{{%flight_quote_label_list}}',
            ['fqll_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-flight_quote_label-updated_user_id',
            '{{%flight_quote_label_list}}',
            ['fqll_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $initData = [
            'SEP' => 'Separate tickets',
            'PUB' => 'Published Fare',
            'SR' => 'Discounted Fare',
            'COMM' => 'Discounted Fare',
            'TOUR' => 'Package Fare',
        ];

        foreach ($initData as $key => $description) {
            $this->insert(
                '{{%flight_quote_label_list}}',
                [
                    'fqll_label_key' => $key,
                    'fqll_origin_description' => $description,
                    'fqll_description' => $description,
                    'fqll_created_dt' => date('Y-m-d H:i:s'),
                    'fqll_updated_dt' => date('Y-m-d H:i:s'),
                    'fqll_created_user_id' => null,
                    'fqll_updated_user_id' => null,
                ]
            );
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_quote_label-created_user_id', '{{%flight_quote_label_list}}');
        $this->dropForeignKey('FK-flight_quote_label-updated_user_id', '{{%flight_quote_label_list}}');

        $this->dropTable('{{%flight_quote_label_list}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
