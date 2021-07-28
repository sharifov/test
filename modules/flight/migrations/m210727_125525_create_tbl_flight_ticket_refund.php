<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210727_125525_create_tbl_flight_ticket_refund
 */
class m210727_125525_create_tbl_flight_ticket_refund extends Migration
{
    private string $tableName = '{{%flight_ticket_refund}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'ftr_id' => $this->primaryKey(),
            'ftr_ticket_number' => $this->string(50)->notNull(),
            'ftr_booking_id' => $this->string(10),
            'ftr_pnr' => $this->string(6),
            'ftr_gds' => $this->string(1),
            'ftr_gds_pcc' => $this->string(255)
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
