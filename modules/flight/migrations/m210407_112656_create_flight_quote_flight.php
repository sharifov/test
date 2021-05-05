<?php

namespace modules\flight\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210407_112656_create_flight_quote_flight
 */
class m210407_112656_create_flight_quote_flight extends Migration
{
    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/flight/flight-quote-flight-crud/index',
        '/flight/flight-quote-flight-crud/create',
        '/flight/flight-quote-flight-crud/view',
        '/flight/flight-quote-flight-crud/update',
        '/flight/flight-quote-flight-crud/delete',
        '/flight/flight-quote-ticket-crud/index',
        '/flight/flight-quote-ticket-crud/create',
        '/flight/flight-quote-ticket-crud/view',
        '/flight/flight-quote-ticket-crud/update',
        '/flight/flight-quote-ticket-crud/delete',
    ];

    /**
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%flight_quote_flight}}', [
            'fqf_id' => $this->primaryKey(),
            'fqf_fq_id' => $this->integer(),
            'fqf_record_locator' => $this->string(8),
            'fqf_gds' => $this->string(2),
            'fqf_gds_pcc' => $this->string(10),
            'fqf_type_id' => $this->tinyInteger(),
            'fqf_cabin_class' => $this->string(1),
            'fqf_trip_type_id' => $this->tinyInteger(),
            'fqf_main_airline' => $this->string(2),
            'fqf_fare_type_id' => $this->tinyInteger(),
            'fqf_status_id' => $this->tinyInteger(),
            'fqf_booking_id' => $this->string(50),
            'fqf_pnr' => $this->string(10),
            'fqf_validating_carrier' => $this->string(2),
            'fqf_original_data_json' => $this->json(),
            'fqf_created_dt' => $this->dateTime(),
            'fqf_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-flight_quote_flight-fqf_fq_id',
            '{{%flight_quote_flight}}',
            ['fqf_fq_id'],
            '{{%flight_quote}}',
            ['fq_id'],
            'SET NULl',
            'CASCADE'
        );

        $this->addColumn('{{%flight_quote_pax_price}}', 'qpp_flight_id', $this->integer());
        $this->addForeignKey(
            'FK-flight_quote_pax_price-qpp_flight_id',
            '{{%flight_quote_pax_price}}',
            ['qpp_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'SET NULl',
            'CASCADE'
        );

        $this->addColumn('{{%flight_quote_segment}}', 'fqs_flight_id', $this->integer());
        $this->addForeignKey(
            'FK-flight_quote_segment-fqs_flight_id',
            '{{%flight_quote_segment}}',
            ['fqs_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'SET NULl',
            'CASCADE'
        );

        $this->addColumn('{{%flight_quote_segment_stop}}', 'qss_flight_id', $this->integer());
        $this->addForeignKey(
            'FK-flight_quote_segment_stop-qss_flight_id',
            '{{%flight_quote_segment_stop}}',
            ['qss_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'SET NULl',
            'CASCADE'
        );

        $this->addColumn('{{%flight_quote_trip}}', 'fqp_flight_id', $this->integer());
        $this->addForeignKey(
            'FK-flight_quote_trip-fqp_flight_id',
            '{{%flight_quote_trip}}',
            ['fqp_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'SET NULl',
            'CASCADE'
        );

        $this->createTable('{{%flight_quote_ticket}}', [
            'fqt_pax_id' => $this->integer()->notNull(),
            'fqt_flight_id' => $this->integer()->notNull(),
            'fqt_ticket_number' => $this->string(50),
            'fqf_created_dt' => $this->dateTime(),
            'fqf_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-flight_quote_ticket-fqt_pax_id-fqt_flight_id', '{{%flight_quote_ticket}}', ['fqt_pax_id', 'fqt_flight_id']);
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_flight_id',
            '{{%flight_quote_ticket}}',
            ['fqt_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_pax_id',
            '{{%flight_quote_ticket}}',
            ['fqt_pax_id'],
            '{{%flight_pax}}',
            ['fp_id'],
            'CASCADE',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('FK-flight_quote_flight-fqf_fq_id', '{{%flight_quote_flight}}');
        $this->dropForeignKey('FK-flight_quote_pax_price-qpp_flight_id', '{{%flight_quote_pax_price}}');
        $this->dropForeignKey('FK-flight_quote_segment_stop-qss_flight_id', '{{%flight_quote_segment_stop}}');
        $this->dropForeignKey('FK-flight_quote_trip-fqp_flight_id', '{{%flight_quote_trip}}');
        $this->dropForeignKey('FK-flight_quote_ticket-fqt_flight_id', '{{%flight_quote_ticket}}');
        $this->dropForeignKey('FK-flight_quote_ticket-fqt_pax_id', '{{%flight_quote_ticket}}');
        $this->dropForeignKey('FK-flight_quote_segment-fqs_flight_id', '{{%flight_quote_segment}}');

        $this->dropColumn('{{%flight_quote_pax_price}}', 'qpp_flight_id');
        $this->dropColumn('{{%flight_quote_segment}}', 'fqs_flight_id');
        $this->dropColumn('{{%flight_quote_segment_stop}}', 'qss_flight_id');
        $this->dropColumn('{{%flight_quote_trip}}', 'fqp_flight_id');

        $this->dropTable('{{%flight_quote_ticket}}');
        $this->dropTable('{{%flight_quote_flight}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
