<?php

use yii\db\Migration;

/**
 * Class m191014_122215_add_column_status_tbl_client_email
 */
class m191014_122215_add_column_status_tbl_client_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_email}}', 'type', $this->tinyInteger(2)->defaultValue(null));

		Yii::$app->db->getSchema()->refreshTableSchema('{{%client_email}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%client_email}}', 'type');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
