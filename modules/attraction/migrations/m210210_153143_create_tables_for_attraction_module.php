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
        '/attraction/attraction/*',
        '/attraction/attraction-quote/*',
    ];

    public $processingFeeAmount = ['processing_fee_amount' => 15];

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

        $this->insert('{{%product_type}}', [
            'pt_id'             => 5,
            'pt_key'            => 'attraction',
            'pt_name'           => 'Attraction',
            'pt_enabled'        => false,
            'pt_settings'       => json_encode($this->processingFeeAmount),
            'pt_service_fee_percent' => 2.5,
            'pt_created_dt'     => date('Y-m-d H:i:s'),
            'pt_updated_dt'     => date('Y-m-d H:i:s'),
        ]);

        $this->createTable('{{%attraction}}', [
            'atn_id'          => $this->primaryKey(),
            'atn_product_id'  => $this->integer(),
            'atn_date_from'   => $this->date(),
            'atn_date_to'     => $this->date(),
            'atn_destination' => $this->string(100),
            'atn_destination_code' => $this->string(10),
            'atn_request_hash_key' => $this->string(32),

        ], $tableOptions);

        $this->addForeignKey('FK-attraction-atn_product_id', '{{%attraction}}', ['atn_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%attraction_quote}}', [
            'atnq_id'                     => $this->primaryKey(),
            'atnq_attraction_id'          => $this->integer()->notNull(),
            'atnq_hash_key'               => $this->string(32)->unique(),
            'atnq_product_quote_id'       => $this->integer(),
            'atnq_json_response'          => $this->json(),
            'atnq_booking_id'             => $this->string(100),
            'atnq_attraction_name'        => $this->string(255),
            'atnq_supplier_name'          => $this->string(255),
            'atnq_type_name'          => $this->string(100),

        ], $tableOptions);

        $this->addForeignKey('FK-attraction_quote-atnq_attraction_id', '{{%attraction_quote}}', ['atnq_attraction_id'], '{{%attraction}}', ['atn_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-attraction_quote-atnq_product_quote_id', '{{%attraction_quote}}', ['atnq_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%attraction_quote}}');
        $this->dropTable('{{%attraction}}');
        $this->delete('{{%product_type}}', ['IN', 'pt_key', ['attraction']]);

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
