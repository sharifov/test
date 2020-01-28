<?php

namespace modules\hotel\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;
/**
 * Class m200127_155405_create_table_hotel_quote_room_pax
 */
class m200127_155405_create_table_hotel_quote_room_pax extends Migration
{
    public $routes = [
        '/hotel/hotel-quote-room-pax-crud/*',
        '/hotel/hotel-quote/ajax-book',
        '/hotel/hotel-quote/ajax-cancel-book',
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
        // table hotel_quote_room_pax
        $this->createTable(
            '{{%hotel_quote_room_pax}}',
            [
                'hqrp_id' => $this->primaryKey(),
                'hqrp_hotel_quote_room_id' => $this->integer()->notNull(),
                'hqrp_type_id' => $this->tinyInteger()->notNull(),
                'hqrp_age' => $this->tinyInteger(2),
                'hqrp_first_name' => $this->string(40),
                'hqrp_last_name' => $this->string(40),
                'hqrp_dob' => $this->date(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'FK-hotel_quote_room_pax-hqrp_hotel_quote_room_id',
            '{{%hotel_quote_room_pax}}', ['hqrp_hotel_quote_room_id'],
            '{{%hotel_quote_room}}', ['hqr_id'],
            'CASCADE',
            'CASCADE');

        $this->createIndex('IDX-hotel_quote_room_pax-hqrp_type_id', '{{%hotel_quote_room_pax}}', ['hqrp_type_id']);

        // table hotel_quote_room
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_children_ages', $this->string(50));
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_rate_comments_id', $this->string(50));
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_rate_comments', $this->string(255));
        $this->addColumn(
            '{{%hotel_quote_room}}',
            'hqr_type',
            $this->tinyInteger(2)->notNull()->defaultValue(0) // property in in model (0 RECHECK/ 1 BOOKABLE)
        );
        $this->createIndex('IDX-hotel_quote_room-hqr_type', '{{%hotel_quote_room}}', ['hqr_type']);

        // table hotel_quote
        $this->addColumn('{{%hotel_quote}}', 'hq_booking_id', $this->string(100)); // field "reference" from response
        $this->addColumn('{{%hotel_quote}}', 'hq_json_booking', $this->json());

        // RBAC
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // table hotel_quote_room_pax
        $this->dropForeignKey('FK-hotel_quote_room_pax-hqrp_hotel_quote_room_id', '{{%hotel_quote_room_pax}}');
        $this->dropIndex('IDX-hotel_quote_room_pax-hqrp_type_id', '{{%hotel_quote_room_pax}}');
        $this->dropTable('{{%hotel_quote_room_pax}}');

        // table hotel_quote_room
        $this->dropIndex('IDX-hotel_quote_room-hqr_type', '{{%hotel_quote_room}}');

        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_children_ages');
        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_rate_comments_id');
        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_rate_comments');
        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_type');

        // table hotel_quote
        $this->dropColumn('{{%hotel_quote}}', 'hq_booking_id');
        $this->dropColumn('{{%hotel_quote}}', 'hq_json_booking');

        // RBAC
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
