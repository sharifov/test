<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quote_flight_price}}`.
 */
class m220915_134521_create_quote_flight_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quote_flight_price}}', [
            'fqp_id' => $this->primaryKey(),
            'fqp_qp_id' => $this->integer()->notNull(),
            'fqp_qf_id' => $this->integer()->notNull(),
            'fqp_qfp_id' => $this->integer()->notNull(),
            'fqp_miles' => $this->integer()->notNull(),
            'fqp_ppm' => $this->decimal(10, 4),
            'fqp_created_dt' => $this->dateTime(),
            'fqp_updated_dt' => $this->dateTime(),
        ]);

        $this->addForeignKey('FK-quote_flight_price-quote_price', '{{%quote_flight_price}}', 'fqp_qp_id', '{{%quote_price}}', 'id', 'CASCADE');
        $this->addForeignKey('FK-quote_flight_price-quote_flight', '{{%quote_flight_price}}', 'fqp_qf_id', '{{%quote_flight}}', 'qf_id', 'CASCADE');
        $this->addForeignKey('FK-quote_flight_price-quote_flight_program', '{{%quote_flight_price}}', 'fqp_qfp_id', '{{%quote_flight_program}}', 'gfp_id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quote_flight_price}}');
    }
}
