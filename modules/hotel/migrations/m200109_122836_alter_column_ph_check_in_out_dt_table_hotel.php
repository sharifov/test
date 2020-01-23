<?php
namespace modules\hotel\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200109_122836_alter_column_ph_check_in_out_dt_table_hotel
 */
class m200109_122836_alter_column_ph_check_in_out_dt_table_hotel extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->alterColumn('{{%hotel}}', 'ph_check_in_dt', $this->date());
		$this->alterColumn('{{%hotel}}', 'ph_check_out_dt', $this->date());

		$this->renameColumn('{{%hotel}}', 'ph_check_in_dt', 'ph_check_in_date');
		$this->renameColumn('{{%hotel}}', 'ph_check_out_dt', 'ph_check_out_date');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel}}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->alterColumn('{{%hotel}}', 'ph_check_in_date', $this->dateTime());
		$this->alterColumn('{{%hotel}}', 'ph_check_out_date', $this->dateTime());

		$this->renameColumn('{{%hotel}}', 'ph_check_in_date', 'ph_check_in_dt');
		$this->renameColumn('{{%hotel}}', 'ph_check_out_date', 'ph_check_out_dt');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel}}');
	}
}
