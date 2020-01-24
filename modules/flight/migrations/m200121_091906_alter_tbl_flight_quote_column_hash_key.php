<?php
namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m200121_091906_alter_tbl_flight_quote_column_hash_key
 */
class m200121_091906_alter_tbl_flight_quote_column_hash_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropIndex('fq_hash_key', '{{%flight_quote}}');
		$this->alterColumn('{{%flight_quote}}', 'fq_hash_key', $this->string(32));
		$this->createIndex('idx-unique-fq_flight_id-fq_hash_key', '{{%flight_quote}}', 'fq_flight_id, fq_hash_key');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-flight_quote-fq_flight_id', '{{%flight_quote}}');
		$this->dropIndex('idx-unique-fq_flight_id-fq_hash_key', '{{%flight_quote}}');
		$this->alterColumn('{{%flight_quote}}', 'fq_hash_key', $this->string(32)->unique());
		$this->addForeignKey('FK-flight_quote-fq_flight_id', '{{%flight_quote}}', ['fq_flight_id'], '{{%flight}}', ['fl_id'], 'CASCADE', 'CASCADE');
	}
}
