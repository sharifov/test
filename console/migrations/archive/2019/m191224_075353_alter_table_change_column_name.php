<?php

use yii\db\Migration;

/**
 * Class m191224_075353_alter_table_change_column_name
 */
class m191224_075353_alter_table_change_column_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn('currency_history', 'cur_his_code', 'ch_code');
		$this->renameColumn('currency_history', 'cur_his_base_rate', 'ch_base_rate');
		$this->renameColumn('currency_history', 'cur_his_app_rate', 'ch_app_rate');
		$this->renameColumn('currency_history', 'cur_his_app_percent', 'ch_app_percent');
		$this->renameColumn('currency_history', 'cur_his_created', 'ch_created_date');
		$this->renameColumn('currency_history', 'cur_his_main_created_dt', 'ch_main_created_dt');
		$this->renameColumn('currency_history', 'cur_his_main_updated_dt', 'ch_main_updated_dt');
		$this->renameColumn('currency_history', 'cur_his_main_synch_dt', 'ch_main_synch_dt');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%currency_history}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {}
}
