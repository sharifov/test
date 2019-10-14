<?php

use yii\db\Migration;

/**
 * Class m191014_121803_add_column_status_tbl_client_phone
 */
class m191014_121803_add_column_status_tbl_client_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_phone}}', 'status', $this->tinyInteger(2)->defaultValue(null));

		Yii::$app->db->getSchema()->refreshTableSchema('{{%client_phone}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%client_phone}}', 'status');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
