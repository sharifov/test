<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210310_123108_create_tbl_flight_quote_option
 */
class m210310_123108_create_tbl_flight_quote_option extends Migration
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

        $this->createTable('{{%flight_quote_option}}', [
            'fqo_id' => $this->primaryKey(),
            'fqo_product_quote_option_id' => $this->integer()->notNull(),
            'fqo_flight_pax_id' => $this->integer(),
            'fqo_flight_quote_segment_id' => $this->integer(),
            'fqo_flight_quote_trip_id' => $this->integer(),
            'fqo_display_name' => $this->string(),
            'fqo_markup_amount' => $this->decimal(10, 2),
            'fqo_base_price' => $this->decimal(10, 2),
            'fqo_total_price' => $this->decimal(10, 2),
            'fqo_client_total' => $this->decimal(10, 2),
            'fqo_created_dt' => $this->dateTime(),
            'fqo_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-flight_quote_option-fqo_product_quote_option_id', '{{%flight_quote_option}}', 'fqo_product_quote_option_id', '{{%product_quote_option}}', 'pqo_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_option-fqo_flight_pax_id', '{{%flight_quote_option}}', 'fqo_flight_pax_id', '{{%flight_pax}}', 'fp_id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_option-fqo_flight_quote_segment_id', '{{%flight_quote_option}}', 'fqo_flight_quote_segment_id', '{{%flight_quote_segment}}', 'fqs_id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-flight_quote_option-fqo_flight_quote_trip_id', '{{%flight_quote_option}}', 'fqo_flight_quote_trip_id', '{{%flight_quote_trip}}', 'fqt_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_quote_option-fqo_product_quote_option_id', '{{%flight_quote_option}}');
        $this->dropForeignKey('FK-flight_quote_option-fqo_flight_pax_id', '{{%flight_quote_option}}');
        $this->dropForeignKey('FK-flight_quote_option-fqo_flight_quote_segment_id', '{{%flight_quote_option}}');
        $this->dropForeignKey('FK-flight_quote_option-fqo_flight_quote_trip_id', '{{%flight_quote_option}}');

        $this->dropTable('{{%flight_quote_option}}');
    }
}
