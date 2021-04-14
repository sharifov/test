<?php

namespace modules\flight\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210414_050550_create_flight_quote_booking_tbl
 */
class m210414_050550_create_flight_quote_booking_tbl extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/flight/flight-quote-booking-crud/index',
        '/flight/flight-quote-booking-crud/create',
        '/flight/flight-quote-booking-crud/view',
        '/flight/flight-quote-booking-crud/update',
        '/flight/flight-quote-booking-crud/delete',
        '/flight/flight-quote-booking-airline-crud/index',
        '/flight/flight-quote-booking-airline-crud/create',
        '/flight/flight-quote-booking-airline-crud/view',
        '/flight/flight-quote-booking-airline-crud/update',
        '/flight/flight-quote-booking-airline-crud/delete',
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

        $this->createTable('{{%flight_quote_booking}}', [
            'fqb_id' => $this->primaryKey(),
            'fqb_fqf_id' => $this->integer()->notNull(),
            'fqb_booking_id' => $this->string(10),
            'fqb_pnr' => $this->string(6),
            'fqb_gds' => $this->string(1),
            'fqb_gds_pcc' => $this->string(),
            'fqb_validating_carrier' => $this->string(2),
            'fqb_created_dt' => $this->dateTime(),
            'fqb_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-flight_quote_booking-fqb_fqf_id',
            '{{%flight_quote_booking}}',
            ['fqb_fqf_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%flight_quote_booking_airline}}', [
            'fqba_id' => $this->primaryKey(),
            'fqba_fqb_id' => $this->integer()->notNull(),
            'fqba_record_locator' => $this->string(20),
            'fqba_airline_code' => $this->string(2),
            'fqba_created_dt' => $this->dateTime(),
            'fqba_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-flight_quote_booking_airline-fqba_fqb_id',
            '{{%flight_quote_booking_airline}}',
            ['fqba_fqb_id'],
            '{{%flight_quote_booking}}',
            ['fqb_id'],
            'CASCADE',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_quote_booking_airline-fqba_fqb_id', '{{%flight_quote_booking_airline}}');
        $this->dropForeignKey('FK-flight_quote_booking-fqb_fqf_id', '{{%flight_quote_booking}}');

        $this->dropTable('{{%flight_quote_booking_airline}}');
        $this->dropTable('{{%flight_quote_booking}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
