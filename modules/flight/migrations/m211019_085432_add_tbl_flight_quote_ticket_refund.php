<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m211019_085432_add_tbl_flight_quote_ticket_refund
 */
class m211019_085432_add_tbl_flight_quote_ticket_refund extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%flight_quote_ticket_refund}}', [
            'fqtr_id' => $this->primaryKey(),
            'fqtr_ticket_number' => $this->string(50)->notNull(),
            'fqtr_created_dt' => $this->dateTime(),
            'fqtr_fqb_id' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey('FK-flight_quote_ticket_refund-fqtr_fqb_id', '{{%flight_quote_ticket_refund}}', 'fqtr_fqb_id', '{{flight_quote_booking}}', 'fqb_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_quote_ticket_refund-fqtr_fqb_id', '{{%flight_quote_ticket_refund}}');
        $this->dropTable('{{%flight_quote_ticket_refund}}');
    }
}
