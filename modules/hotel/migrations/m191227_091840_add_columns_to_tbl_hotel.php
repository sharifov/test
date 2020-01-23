<?php
namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m191227_091840_add_columns_to_tbl_hotel
 */
class m191227_091840_add_columns_to_tbl_hotel extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('{{%hotel}}', 'ph_destination_label', $this->string(100)->after('ph_destination_code'));
		$this->addColumn('{{%hotel}}', 'ph_zone_code', $this->integer(11)->after('ph_check_out_date'));
		$this->addColumn('{{%hotel}}', 'ph_hotel_code', $this->integer(11)->after('ph_zone_code'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('{{%hotel}}', 'ph_destination_label');
		$this->dropColumn('{{%hotel}}', 'ph_zone_code');
		$this->dropColumn('{{%hotel}}', 'ph_hotel_code');
	}
}
