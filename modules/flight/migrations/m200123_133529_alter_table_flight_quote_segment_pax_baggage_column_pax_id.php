<?php
namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m200123_133529_alter_table_flight_quote_segment_pax_baggage_column_pax_id
 */
class m200123_133529_alter_table_flight_quote_segment_pax_baggage_column_pax_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->dropForeignKey('FK-flight_quote_segment_pax_baggage_charge-qsbc_flight_pax_id', '{{%flight_quote_segment_pax_baggage_charge}}');
		$this->alterColumn('{{%flight_quote_segment_pax_baggage_charge}}', 'qsbc_flight_pax_id', $this->tinyInteger(1));
    	$this->renameColumn('{{%flight_quote_segment_pax_baggage_charge}}', 'qsbc_flight_pax_id', 'qsbc_flight_pax_code_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->renameColumn('{{%flight_quote_segment_pax_baggage_charge}}', 'qsbc_flight_pax_code_id', 'qsbc_flight_pax_id');
		$this->alterColumn('{{%flight_quote_segment_pax_baggage_charge}}', 'qsbc_flight_pax_id', $this->integer());
		$this->addForeignKey('FK-flight_quote_segment_pax_baggage_charge-qsbc_flight_pax_id', '{{%flight_quote_segment_pax_baggage_charge}}', ['qsbc_flight_pax_id'], '{{%flight_pax}}', ['fp_id'], 'CASCADE', 'CASCADE');
	}
}
