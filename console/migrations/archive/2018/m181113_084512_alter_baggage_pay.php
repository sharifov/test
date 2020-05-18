<?php

use yii\db\Migration;

/**
 * Class m181113_084512_alter_baggage_pay
 */
class m181113_084512_alter_baggage_pay extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quote_segment_baggage_charge}}',[
            'qsbc_id' => $this->primaryKey(),
            'qsbc_pax_code' => $this->string(3),
            'qsbc_segment_id' => $this->integer(),
            'qsbc_first_piece' => $this->integer(2),
            'qsbc_last_piece' => $this->integer(2),
            'qsbc_price' => $this->float(),
            'qsbc_currency' => $this->string(5),
            'qsbc_max_weight' => $this->string(100),
            'qsbc_max_size' => $this->string(100),
            'qsbc_created_dt' => $this->dateTime()
            ->defaultExpression('NOW()'),
            'qsbc_updated_dt' => $this->dateTime(),
            'qsbc_updated_user_id' => $this->integer()
        ]);
        $this->addForeignKey('fk_segment_baggage_charge_updated_user', '{{%quote_segment_baggage_charge}}', 'qsbc_updated_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_quote_segment_baggage_charge', '{{%quote_segment_baggage_charge}}', 'qsbc_segment_id', 'quote_segment', 'qs_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_segment_baggage_charge_updated_user', '{{%quote_segment_baggage_charge}}');
        $this->dropForeignKey('fk_segment_baggage_charge_updated_user', '{{%quote_segment_baggage_charge}}');
        $this->dropTable('{{%quote_segment_baggage_charge}}');
    }
}
