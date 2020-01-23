<?php
namespace modules\hotel\migrations;

use yii\db\Migration;

/**
 * Class m191227_125605_alter_columns_tbl_hotel
 */
class m191227_125605_alter_columns_tbl_hotel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn('{{%hotel}}', 'ph_check_in_date', 'ph_check_in_dt');
		$this->alterColumn('{{%hotel}}', 'ph_check_in_dt', $this->dateTime());

		$this->renameColumn('{{%hotel}}', 'ph_check_out_date', 'ph_check_out_dt');
		$this->alterColumn('{{%hotel}}', 'ph_check_out_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->renameColumn('{{%hotel}}', 'ph_check_in_dt', 'ph_check_in_date');
		$this->alterColumn('{{%hotel}}', 'ph_check_in_date', $this->date());

		$this->renameColumn('{{%hotel}}', 'ph_check_out_dt', 'ph_check_out_date');
		$this->alterColumn('{{%hotel}}', 'ph_check_out_date', $this->date());
    }
}
