<?php

use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%quote_flight}}`.
 */
class m220915_132221_create_quote_flight_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quote_flight}}', [
            'qf_id' => $this->primaryKey(),
            'qf_quote_id' => $this->integer()->notNull(),
            'qf_booking_type' => $this->string(20)->defaultValue(AwardProgramDictionary::REVENUE),
            'qf_user_id' => $this->integer(),
            'qf_record_locator' => $this->string(8),
            'qf_gds' => $this->string(1),
            'qf_gds_pcc' => $this->string(50),
            'qf_cabin' => $this->string(1),
            'qf_trip_type' => $this->string(2),
            'qf_check_payment' => $this->tinyInteger(1),
            'qf_fare_type' => $this->string(20),
            'qf_created_dt' => $this->dateTime(),
            'qf_updated_dt' => $this->dateTime(),
            'qf_updated_user_id' => $this->integer()
        ]);

        $this->addForeignKey('FK-quote_flight-quotes', '{{%quote_flight}}', 'qf_quote_id', '{{%quotes}}', 'id', 'CASCADE');
        $this->addForeignKey('FK-quote_flight-user', '{{%quote_flight}}', 'qf_user_id', '{{%employees}}', 'id', 'SET NULL');
        $this->addForeignKey('FK-quote_flight-updated_user', '{{%quote_flight}}', 'qf_updated_user_id', '{{%employees}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quote_flight}}');
    }
}
