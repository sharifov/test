<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200124_093555_add_hqr_children_ages_to_hotel_quote_room
 */
class m200124_093555_add_hqr_children_ages_to_hotel_quote_room extends Migration
{
    public $routes = [
        '/hotel/hotel-quote-room-pax-crud/*',
        '/hotel/hotel-quote/ajax-book'
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

        $this->addColumn('{{%hotel_quote_room}}', 'hqr_children_ages', $this->string(50));

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-hotel_quote_room_pax-hqrp_hotel_quote_room_id', '{{%hotel_quote_room_pax}}');
        $this->dropIndex('IDX-hotel_quote_room_pax-hqrp_type_id', '{{%hotel_quote_room_pax}}');

        $this->dropTable('{{%hotel_quote_room_pax}}');

        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_children_ages');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
