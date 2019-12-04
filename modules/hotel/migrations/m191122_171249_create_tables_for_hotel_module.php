<?php
namespace modules\hotel\migrations;
use yii\db\Migration;

/**
 * Class m191122_171249_create_tables_for_hotel_module
 */
class m191122_171249_create_tables_for_hotel_module extends Migration
{

    public $routes = [
        '/hotel/default/*',
        '/hotel/hotel/*',
        '/hotel/hotel-room/*',
        '/hotel/hotel-room-pax/*',
        '/hotel/hotel-quote/*',
        '/hotel/hotel-quote-room/*',
        '/hotel/hotel-list/*',
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

        $this->createTable('{{%hotel}}',	[
            'ph_id'                    => $this->primaryKey(),
            'ph_product_id'            => $this->integer(),
            'ph_check_in_date'         => $this->date(),
            'ph_check_out_date'        => $this->date(),
            'ph_destination_code'      => $this->string(10),
            'ph_min_star_rate'         => $this->tinyInteger(),
            'ph_max_star_rate'         => $this->tinyInteger(),
            'ph_max_price_rate'        => $this->tinyInteger(),
            'ph_min_price_rate'        => $this->tinyInteger(),
        ], $tableOptions);

        $this->addForeignKey('FK-hotel-ph_product_id', '{{%hotel}}', ['ph_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');
        //$this->createIndex('IND-offer-of_gid', '{{%offer}}', ['of_gid'], true);


        $this->createTable('{{%hotel_room}}',	[
            'hr_id'             => $this->primaryKey(),
            'hr_hotel_id'       => $this->integer()->notNull(),
            'hr_room_name'      => $this->string(200),
        ], $tableOptions);

        $this->addForeignKey('FK-hotel_room-hr_hotel_id', '{{%hotel_room}}', ['hr_hotel_id'], '{{%hotel}}', ['ph_id'], 'CASCADE', 'CASCADE');

        $this->createTable('{{%hotel_room_pax}}',	[
            'hrp_id'                => $this->primaryKey(),
            'hrp_hotel_room_id'     => $this->integer()->notNull(),
            'hrp_type_id'           => $this->tinyInteger()->notNull(),
            'hrp_age'               => $this->tinyInteger(2),
            'hrp_first_name'        => $this->string(40),
            'hrp_last_name'         => $this->string(40),
            'hrp_dob'               => $this->date(),
        ], $tableOptions);

        $this->addForeignKey('FK-hotel_room_pax-hrp_hotel_room_id', '{{%hotel_room_pax}}', ['hrp_hotel_room_id'], '{{%hotel_room}}', ['hr_id'], 'CASCADE', 'CASCADE');
        $this->createIndex('IND-hotel_room_pax-hrp_type_id', '{{%hotel_room_pax}}', ['hrp_type_id']);


        $this->createTable('{{%hotel_list}}',	[
            'hl_id'                 => $this->primaryKey(),
            'hl_code'               => $this->integer()->unique(),
            'hl_hash_key'           => $this->string(32)->unique(),
            'hl_name'               => $this->string(150)->notNull(),
            'hl_star'               => $this->string(2),

            'hl_category_name'      => $this->string(40),
            'hl_destination_code'   => $this->string(5),
            'hl_destination_name'   => $this->string(150),
            'hl_zone_name'          => $this->string(150),
            'hl_zone_code'          => $this->smallInteger(),
            'hl_country_code'       => $this->string(5),
            'hl_state_code'         => $this->string(5),

            'hl_description'        => $this->text(),
            'hl_address'            => $this->text(),
            'hl_postal_code'        => $this->string(10),
            'hl_city'               => $this->string(150),
            'hl_email'              => $this->string(160),
            'hl_web'                => $this->string(150),

            'hl_phone_list'         => $this->json(),
            'hl_image_list'         => $this->json(),
            'hl_image_base_url'     => $this->string(160),

            'hl_board_codes'        => $this->json(),
            'hl_segment_codes'      => $this->json(),

            'hl_latitude'           => $this->decimal(10, 7),
            'hl_longitude'          => $this->decimal(10, 7),
            'hl_ranking'            => $this->smallInteger(),
            'hl_service_type'       => $this->string(30),
            'hl_last_update'        => $this->date(),
            'hl_created_dt'         => $this->dateTime(),
            'hl_updated_dt'         => $this->dateTime(),

        ], $tableOptions);

        //$this->addForeignKey('FK-hotel_list-hqr_currency', '{{%hotel_list}}', ['hqr_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-hotel_list-hl_code', '{{%hotel_list}}', ['hl_code'], true);
        $this->createIndex('IND-hotel_list-hl_hash_key', '{{%hotel_list}}', ['hl_hash_key'], true);


        $this->createTable('{{%hotel_quote}}',	[
            'hq_id'                     => $this->primaryKey(),
            'hq_hotel_id'               => $this->integer()->notNull(),
            'hq_hash_key'               => $this->string(32)->unique(),
            'hq_product_quote_id'       => $this->integer(),
            'hq_json_response'          => $this->json(),
            'hq_destination_name'       => $this->string(255),
            'hq_hotel_name'             => $this->string(200)->notNull(),
            'hq_hotel_list_id'          => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-hotel_quote-hq_hotel_id', '{{%hotel_quote}}', ['hq_hotel_id'], '{{%hotel}}', ['ph_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-hotel_quote-hq_product_quote_id', '{{%hotel_quote}}', ['hq_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-hotel_quote-hq_hotel_list_id', '{{%hotel_quote}}', ['hq_hotel_list_id'], '{{%hotel_list}}', ['hl_id'], 'SET NULL', 'CASCADE');
        //$this->createIndex('IND-hotel_room_pax-hrp_type_id', '{{%hotel_quote}}', ['hrp_type_id']);

        $this->createTable('{{%hotel_quote_room}}',	[
            'hqr_id'                    => $this->primaryKey(),
            'hqr_hotel_quote_id'        => $this->integer()->notNull(),
            'hqr_room_name'             => $this->string(150),
            'hqr_key'                   => $this->string(255),
            'hqr_code'                  => $this->string(10),
            'hqr_class'                 => $this->string(5),
            'hqr_amount'                => $this->decimal(10, 2),
            'hqr_currency'              => $this->string(3),
            'hqr_cancel_amount'         => $this->decimal(10, 2),
            'hqr_cancel_from_dt'        => $this->dateTime(),
            'hqr_payment_type'          => $this->string(10),
            'hqr_board_code'            => $this->string(2),
            'hqr_board_name'            => $this->string(100),
            'hqr_rooms'                 => $this->tinyInteger(),
            'hqr_adults'                => $this->tinyInteger(),
            'hqr_children'              => $this->tinyInteger(),
        ], $tableOptions);

        $this->addForeignKey('FK-hotel_quote_room-hqr_hotel_quote_id', '{{%hotel_quote_room}}', ['hqr_hotel_quote_id'], '{{%hotel_quote}}', ['hq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-hotel_quote_room-hqr_currency', '{{%hotel_quote_room}}', ['hqr_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%hotel_quote_room}}');
        $this->dropTable('{{%hotel_quote}}');
        $this->dropTable('{{%hotel_list}}');
        $this->dropTable('{{%hotel_room_pax}}');
        $this->dropTable('{{%hotel_room}}');
        $this->dropTable('{{%hotel}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }

}
