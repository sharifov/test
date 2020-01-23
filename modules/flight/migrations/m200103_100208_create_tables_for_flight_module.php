<?php
namespace modules\flight\migrations;
use yii\db\Migration;

/**
 * Class m200103_100208_create_tables_for_flight_module
 */
class m200103_100208_create_tables_for_flight_module extends Migration
{
    public $routes = [
        '/flight/default/*',
        '/flight/flight/*',
        '/flight/flight-segment/*',
        '/flight/flight-quote/*',
        '/flight/flight-quote-trip/*',
        '/flight/flight-quote-segment/*',
        '/flight/flight-pax/*',
        '/flight/flight-quote-segment-stop/*',
        '/flight/flight-quote-segment-pax-baggage-charge/*',
        '/flight/flight-quote-segment-pax-baggage/*',
        '/flight/flight-quote-pax-price/*',
        '/flight/flight-quote-status-log/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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

        $this->createTable('{{%flight}}',	[
            'fl_id'                 => $this->primaryKey(),
            'fl_product_id'         => $this->integer(),
            'fl_trip_type_id'       => $this->tinyInteger(1),
            'fl_cabin_class'        => $this->string(1),
            'fl_adults'             => $this->tinyInteger(2),
            'fl_children'           => $this->tinyInteger(2),
            'fl_infants'            => $this->tinyInteger(2),
        ], $tableOptions);


        $this->addForeignKey('FK-flight-fl_product_id', '{{%flight}}', ['fl_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');
        //$this->createIndex('IND-offer-of_gid', '{{%offer}}', ['of_gid'], true);


        $this->createTable('{{%flight_segment}}',	[
            'fs_id'                     => $this->primaryKey(),
            'fs_flight_id'              => $this->integer()->notNull(),
            'fs_origin_iata'            => $this->tinyInteger(3)->notNull(),
            'fs_destination_iata'       => $this->string(3)->notNull(),
            'fs_departure_date'         => $this->date()->notNull(),
            'fs_flex_type_id'           => $this->tinyInteger(1),
            'fs_flex_days'              => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addForeignKey('FK-flight_segment-fs_flight_id', '{{%flight_segment}}', ['fs_flight_id'], '{{%flight}}', ['fl_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%flight_quote}}',	[
            'fq_id'                     => $this->primaryKey(),
            'fq_flight_id'              => $this->integer()->notNull(),
            'fq_source_id'              => $this->tinyInteger(1),
            'fq_product_quote_id'       => $this->integer(),
            'fq_hash_key'               => $this->string(32)->unique(),
            'fq_service_fee_percent'    => $this->decimal(5, 2)->defaultValue(3.5),

            'fq_record_locator'         => $this->string(8),
            'fq_gds'                    => $this->string(2),
            'fq_gds_pcc'                => $this->string(10),
            'fq_gds_offer_id'           => $this->tinyInteger(1),
            'fq_type_id'                => $this->tinyInteger(1),

            'fq_cabin_class'            => $this->string(1),
            'fq_trip_type_id'           => $this->tinyInteger(1),
            'fq_main_airline'           => $this->string(2),
            'fq_fare_type_id'           => $this->tinyInteger(1),
            //'fq_origin_search_data'     => $this->text(),

            'fq_created_user_id'        => $this->integer(),
            'fq_created_expert_id'      => $this->integer(),
            'fq_created_expert_name'      => $this->string(20),
            'fq_reservation_dump'       => $this->text(),
            'fq_pricing_info'           => $this->text(),
            'fq_origin_search_data'     => $this->json(),
            'fq_last_ticket_date'       => $this->date(),
        ], $tableOptions);


        $this->addForeignKey('FK-flight_quote-fq_flight_id', '{{%flight_quote}}', ['fq_flight_id'], '{{%flight}}', ['fl_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote-fq_product_quote_id', '{{%flight_quote}}', ['fq_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote-fq_created_user_id', '{{%flight_quote}}', ['fq_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');



        $this->createTable('{{%flight_quote_trip}}',	[
            'fqt_id'                => $this->primaryKey(),
            'fqt_key'               => $this->string(255),
            'fqt_flight_quote_id'   => $this->integer()->notNull(),
            'fqt_duration'          => $this->integer(),
        ], $tableOptions);


        $this->addForeignKey('FK-flight_quote_trip-fqt_flight_quote_id', '{{%flight_quote_trip}}', ['fqt_flight_quote_id'], '{{%flight_quote}}', ['fq_id'], 'CASCADE', 'CASCADE');



        $this->createTable('{{%flight_quote_segment}}',	[
            'fqs_id'                        => $this->primaryKey(),
            'fqs_flight_quote_id'           => $this->integer()->notNull(),
            'fqs_flight_quote_trip_id'      => $this->integer(),
            'fqs_departure_dt'              => $this->dateTime()->notNull(),
            'fqs_arrival_dt'                => $this->dateTime()->notNull(),
            'fqs_stop'                      => $this->tinyInteger(1)->defaultValue(0),
            'fqs_flight_number'             => $this->smallInteger(),
            'fqs_booking_class'             => $this->string(1),
            'fqs_duration'                  => $this->smallInteger(),
            'fqs_departure_airport_iata'        => $this->string(3)->notNull(),
            'fqs_departure_airport_terminal'    => $this->string(3),
            'fqs_arrival_airport_iata'          => $this->string(3)->notNull(),
            'fqs_arrival_airport_terminal'  => $this->string(3),
            'fqs_operating_airline'         => $this->string(2),
            'fqs_marketing_airline'         => $this->string(2),
            'fqs_air_equip_type'            => $this->string(4),
            'fqs_marriage_group'            => $this->string(2),
            'fqs_cabin_class'               => $this->string(2),
            'fqs_meal'                      => $this->string(2),
            'fqs_fare_code'                 => $this->string(20),
            'fqs_key'                       => $this->string(40),
            'fqs_ticket_id'                 => $this->tinyInteger(1),
            'fqs_recheck_baggage'           => $this->boolean()->defaultValue(false),
            'fqs_mileage'                   => $this->smallInteger()
        ], $tableOptions);


        $this->addForeignKey('FK-flight_quote_segment-fqs_flight_quote_id', '{{%flight_quote_segment}}', ['fqs_flight_quote_id'], '{{%flight_quote}}', ['fq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_segment-fqs_flight_quote_trip_id', '{{%flight_quote_segment}}', ['fqs_flight_quote_trip_id'], '{{%flight_quote_trip}}', ['fqt_id'], 'SET NULL', 'CASCADE');


        $this->createTable('{{%flight_quote_segment_stop}}',	[
            'qss_id'                        => $this->primaryKey(),
            'qss_quote_segment_id'          => $this->integer()->notNull(),
            'qss_location_iata'             => $this->string(3),
            'qss_equipment'                 => $this->string(5),
            'qss_elapsed_time'              => $this->integer(),
            'qss_duration'                  => $this->integer(),
            'qss_departure_dt'              => $this->dateTime(),
            'qss_arrival_dt'                => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-flight_quote_segment_stop-qss_quote_segment_id', '{{%flight_quote_segment_stop}}', ['qss_quote_segment_id'], '{{%flight_quote_segment}}', ['fqs_id'], 'CASCADE', 'CASCADE');


         $this->createTable('{{%flight_pax}}',	[
             'fp_id'                        => $this->primaryKey(),
             'fp_flight_id'                 => $this->integer()->notNull(),
             'fp_pax_id'                    => $this->integer(),
             'fp_pax_type'                  => $this->string(3),
             'fp_first_name'                => $this->string(40),
             'fp_last_name'                 => $this->string(40),
             'fp_middle_name'               => $this->string(40),
             'fp_dob'                       => $this->date(),
         ], $tableOptions);


        $this->addForeignKey('FK-flight_pax-fp_flight_id', '{{%flight_pax}}', ['fp_flight_id'], '{{%flight}}', ['fl_id'], 'CASCADE', 'CASCADE');
        //$this->addForeignKey('FK-flight_pax-fq_product_quote_id', '{{%flight_pax}}', ['fq_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');


        $this->createTable('{{%flight_quote_segment_pax_baggage}}',	[
            'qsb_id'                        => $this->primaryKey(),
            'qsb_flight_pax_code_id'        => $this->tinyInteger(1)->notNull(),
            'qsb_flight_quote_segment_id'   => $this->integer()->notNull(),
            'qsb_airline_code'              => $this->string(3),
            'qsb_allow_pieces'              => $this->tinyInteger(1),
            'qsb_allow_weight'              => $this->tinyInteger(2),
            'qsb_allow_unit'                => $this->string(4),
            'qsb_allow_max_weight'          => $this->string(100),
            'qsb_allow_max_size'            => $this->string(100),
        ], $tableOptions);


        //$this->addForeignKey('FK-flight_quote_segment_pax_baggage-qsb_flight_pax_code_id', '{{%flight_quote_segment_pax_baggage}}', ['qsb_flight_pax_code_id'], '{{%flight_pax_code}}', ['fpc_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_segment_pax_baggage-qsb_flight_quote_segment_id', '{{%flight_quote_segment_pax_baggage}}', ['qsb_flight_quote_segment_id'], '{{%flight_quote_segment}}', ['fqs_id'], 'CASCADE', 'CASCADE');



        $this->createTable('{{%flight_quote_segment_pax_baggage_charge}}',	[
            'qsbc_id'                       => $this->primaryKey(),
            'qsbc_flight_pax_id'            => $this->integer()->notNull(),
            'qsbc_flight_quote_segment_id'  => $this->integer()->notNull(),
            'qsbc_first_piece'              => $this->tinyInteger(1),
            'qsbc_last_piece'               => $this->tinyInteger(1),
            'qsbc_origin_price'             => $this->decimal(10, 2),
            'qsbc_origin_currency'          => $this->string(3),
            'qsbc_price'                    => $this->decimal(10, 2),
            'qsbc_client_price'             => $this->decimal(10, 2),
            'qsbc_client_currency'          => $this->string(3),
            'qsbc_max_weight'               => $this->string(100),
            'qsbc_max_size'                 => $this->string(100),
        ], $tableOptions);


        $this->addForeignKey('FK-flight_quote_segment_pax_baggage_charge-qsbc_flight_pax_id', '{{%flight_quote_segment_pax_baggage_charge}}', ['qsbc_flight_pax_id'], '{{%flight_pax}}', ['fp_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_segment_pax_baggage_charge-quote_segment_id', '{{%flight_quote_segment_pax_baggage_charge}}', ['qsbc_flight_quote_segment_id'], '{{%flight_quote_segment}}', ['fqs_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_segment_pax_baggage_charge-qsbc_client_currency', '{{%flight_quote_segment_pax_baggage_charge}}', ['qsbc_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_segment_pax_baggage_charge-qsbc_origin_currency', '{{%flight_quote_segment_pax_baggage_charge}}', ['qsbc_origin_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');


        $this->createTable('{{%flight_quote_pax_price}}',	[
            'qpp_id'                    => $this->primaryKey(),
            'qpp_flight_quote_id'       => $this->integer()->notNull(),
            'qpp_flight_pax_code_id'    => $this->tinyInteger(1)->notNull(),
            'qpp_fare'                  => $this->decimal(10, 2)->defaultValue(0),
            'qpp_tax'                   => $this->decimal(10, 2)->defaultValue(0),
            'qpp_system_mark_up'        => $this->decimal(10, 2)->defaultValue(0),
            'qpp_agent_mark_up'         => $this->decimal(10, 2)->defaultValue(0),
            'qpp_origin_fare'           => $this->decimal(10, 2),
            'qpp_origin_currency'       => $this->string(3),
            'qpp_origin_tax'            => $this->decimal(10, 2),
            'qpp_client_currency'       => $this->string(3),
            'qpp_client_fare'           => $this->decimal(10, 2),
            'qpp_client_tax'            => $this->decimal(10, 2),
            'qpp_created_dt'            => $this->dateTime(),
            'qpp_updated_dt'            => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey('FK-flight_quote_pax_price-qpp_flight_quote_id', '{{%flight_quote_pax_price}}', ['qpp_flight_quote_id'], '{{%flight_quote}}', ['fq_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-flight_quote_pax_price-qpp_origin_currency', '{{%flight_quote_pax_price}}', ['qpp_origin_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_pax_price-qpp_client_currency', '{{%flight_quote_pax_price}}', ['qpp_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        //$this->addForeignKey('FK-flight_quote_pax_price-qpp_flight_pax_code_id', '{{%flight_quote_pax_price}}', ['qpp_flight_pax_code_id'], '{{%flight_pax_code}}', ['fpc_id'], 'CASCADE', 'CASCADE');


        $this->createTable('{{%flight_quote_status_log}}',	[
            'qsl_id'                   => $this->primaryKey(),
            'qsl_created_user_id'      => $this->integer(),
            'qsl_flight_quote_id'      => $this->integer()->notNull(),
            'qsl_status_id'            => $this->tinyInteger(1),
            'qsl_created_dt'           => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-flight_quote_status_log-qsl_flight_quote_id', '{{%flight_quote_status_log}}', ['qsl_flight_quote_id'], '{{%flight_quote}}', ['fq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_status_log-qsl_created_user_id', '{{%flight_quote_status_log}}', ['qsl_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-flight_quote_status_log-qsl_flight_quote_id', '{{%flight_quote_status_log}}', ['qsl_flight_quote_id', 'qsl_status_id']);

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%flight_quote_status_log}}');
        $this->dropTable('{{%flight_quote_pax_price}}');
        $this->dropTable('{{%flight_quote_segment_pax_baggage_charge}}');
        $this->dropTable('{{%flight_quote_segment_pax_baggage}}');
        $this->dropTable('{{%flight_quote_segment_stop}}');
        $this->dropTable('{{%flight_pax}}');
        $this->dropTable('{{%flight_quote_segment}}');
        $this->dropTable('{{%flight_quote_trip}}');
        $this->dropTable('{{%flight_quote}}');
        $this->dropTable('{{%flight_segment}}');
        $this->dropTable('{{%flight}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
